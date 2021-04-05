<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class Todos extends Component
{
    
    public $todos, $title, $desc, $todo_id;
    public $isOpen = 0;

    public function render()
    {
        $this->todos = Todo::where('user_id', Auth::user()->id)->orderBy('done')->get();
        return view('livewire.todos');
    }

    public function hasDone($id) {
        $todo = Todo::find($id);
        $todo->done = !$todo->done;
        $todo->save();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal() {
        $this->isOpen = true;
    }

    public function closeModal() {
        $this->isOpen = false;
    }

    public function resetInputFields() {
        $this->title = '';
        $this->desc = '';
        $this->todo_id = '';
    }

    public function store() {
        $this->validate([
            'title' => 'required',
            'desc' => 'required|min:6'
        ]);

        Todo::updateOrCreate([
            'id' => $this->todo_id,
        ], [
            'title' => $this->title,
            'description' => $this->desc,
            'user_id' => Auth::user()->id
        ]);

        session()->flash('message', $this->todo_id ? 'Todo Updated Successfully.' : 'Todo Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id) {
        $todo = Todo::findOrFail($id);
        $this->todo_id = $id;
        $this->title = $todo->title;
        $this->desc = $todo->description;

        $this->openModal();
    }

    public function delete($id) {
        Todo::find($id)->delete();
        session()->flash('message', 'Todo Deleted Successfully.');
    }
}
