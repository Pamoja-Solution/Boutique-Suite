<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ExpenseManager extends Component
{
    use WithPagination, WithFileUploads;

    public $amount;
    public $type = 'expense';
    public $motif;
    public $attachment;
    public $expenseId;
    public $isOpen = false;
    public $search = '';
    public $confirmingDeletion = false;

    protected $rules = [
        'amount' => 'required|numeric',
        'type' => 'required|in:expense,income',
        'motif' => 'required|string',
        'attachment' => 'nullable|file|max:5048',
    ];

    public function render()
    {
        $expenses = Expense::where('user_id',Auth::user()->id)
            ->when($this->search, function ($query) {
                $query->where('motif', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.expense-manager', [
            'expenses' => $expenses,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetErrorBag();
    }

    public function resetInputFields()
    {
        $this->expenseId = null;
        $this->amount = '';
        $this->type = 'expense';
        $this->motif = '';
        $this->attachment = null;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'amount' => $this->amount,
            'type' => $this->type,
            'motif' => $this->motif,
            'user_id' => Auth::user()->id,
        ];

        if ($this->attachment) {
            $path = $this->attachment->store('expenses/attachments', 'public');
            $data['attachment_path'] = $path;
        }

        if ($this->expenseId) {
            $expense = Expense::find($this->expenseId);
            if ($expense->attachment_path && $this->attachment) {
                Storage::disk('public')->delete($expense->attachment_path);
            }
            $expense->update($data);
            session()->flash('message', 'Dépense mise à jour avec succès.');
        } else {
            Expense::create($data);
            session()->flash('message', 'Dépense créée avec succès.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $this->expenseId = $id;
        $this->amount = $expense->amount;
        $this->type = $expense->type;
        $this->motif = $expense->motif;
        
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = $id;
    }

    public function delete($id)
    {
        $expense = Expense::find($id);
        if ($expense->attachment_path) {
            Storage::disk('public')->delete($expense->attachment_path);
        }
        $expense->delete();
        $this->confirmingDeletion = false;
        session()->flash('message', 'Dépense supprimée avec succès.');
    }
}