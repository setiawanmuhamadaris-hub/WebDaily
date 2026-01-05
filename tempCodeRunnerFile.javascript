import React, { useState } from 'react';
import { Trash2, Plus, Check } from 'lucide-react';

export default function TodoApp() {
  const [todos, setTodos] = useState([]);
  const [input, setInput] = useState('');

  const addTodo = () => {
    if (input.trim()) {
      setTodos([...todos, { id: Date.now(), text: input, completed: false }]);
      setInput('');
    }
  };

  const toggleTodo = (id) => {
    setTodos(todos.map(todo => 
      todo.id === id ? { ...todo, completed: !todo.completed } : todo
    ));
  };

  const deleteTodo = (id) => {
    setTodos(todos.filter(todo => todo.id !== id));
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter') {
      addTodo();
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-500 to-purple-600 p-4">
      <div className="max-w-md mx-auto mt-8">
        {/* Header */}
        <div className="bg-white rounded-t-2xl p-6 shadow-lg">
          <h1 className="text-3xl font-bold text-gray-800 text-center mb-2">
            📝 My Todo List
          </h1>
          <p className="text-gray-500 text-center text-sm">
            Kelola tugas harian Anda
          </p>
        </div>

        {/* Input Area */}
        <div className="bg-white p-4 shadow-md">
          <div className="flex gap-2">
            <input
              type="text"
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyPress={handleKeyPress}
              placeholder="Tambah tugas baru..."
              className="flex-1 px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 transition"
            />
            <button
              onClick={addTodo}
              className="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition shadow-md"
            >
              <Plus size={20} />
            </button>
          </div>
        </div>

        {/* Todo List */}
        <div className="bg-white rounded-b-2xl shadow-lg overflow-hidden">
          {todos.length === 0 ? (
            <div className="p-12 text-center text-gray-400">
              <p className="text-lg">Belum ada tugas</p>
              <p className="text-sm mt-2">Tambahkan tugas pertama Anda!</p>
            </div>
          ) : (
            <div className="divide-y divide-gray-100">
              {todos.map((todo) => (
                <div
                  key={todo.id}
                  className="p-4 hover:bg-gray-50 transition flex items-center gap-3"
                >
                  <button
                    onClick={() => toggleTodo(todo.id)}
                    className={`w-6 h-6 rounded-full border-2 flex items-center justify-center transition ${
                      todo.completed
                        ? 'bg-green-500 border-green-500'
                        : 'border-gray-300 hover:border-green-400'
                    }`}
                  >
                    {todo.completed && <Check size={16} className="text-white" />}
                  </button>
                  
                  <span
                    className={`flex-1 ${
                      todo.completed
                        ? 'line-through text-gray-400'
                        : 'text-gray-800'
                    }`}
                  >
                    {todo.text}
                  </span>
                  
                  <button
                    onClick={() => deleteTodo(todo.id)}
                    className="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition"
                  >
                    <Trash2 size={18} />
                  </button>
                </div>
              ))}
            </div>
          )}
          
          {/* Summary */}
          {todos.length > 0 && (
            <div className="bg-gray-50 p-4 text-center text-sm text-gray-600">
              {todos.filter(t => t.completed).length} dari {todos.length} tugas selesai
            </div>
          )}
        </div>
      </div>
    </div>
  );
}