import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, Save, X, GripVertical } from 'lucide-react';
import { supabase } from '../../lib/supabase';

interface Category {
  id: string;
  name: string;
  slug: string;
  icon: string;
  color: string;
  description: string | null;
  sort_order: number;
  is_active: boolean;
}

export function CategoryManagement() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [editForm, setEditForm] = useState<Partial<Category>>({});
  const [isAdding, setIsAdding] = useState(false);
  const [newCategory, setNewCategory] = useState({ name: '', slug: '', icon: '📁', color: '#3B82F6', description: '' });

  useEffect(() => {
    loadCategories();
  }, []);

  const loadCategories = async () => {
    const { data, error } = await supabase!
      .from('travelc_categories')
      .select('*')
      .order('sort_order', { ascending: true });
    
    if (!error && data) {
      setCategories(data);
    }
    setLoading(false);
  };

  const handleAdd = async () => {
    if (!newCategory.name.trim()) return;
    
    const slug = newCategory.slug || newCategory.name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    const maxOrder = Math.max(...categories.map(c => c.sort_order), 0);
    
    const { error } = await supabase!
      .from('travelc_categories')
      .insert({
        name: newCategory.name,
        slug,
        icon: newCategory.icon,
        color: newCategory.color,
        description: newCategory.description || null,
        sort_order: maxOrder + 1
      });
    
    if (!error) {
      loadCategories();
      setIsAdding(false);
      setNewCategory({ name: '', slug: '', icon: '📁', color: '#3B82F6', description: '' });
    }
  };

  const handleEdit = (cat: Category) => {
    setEditingId(cat.id);
    setEditForm({ ...cat });
  };

  const handleSave = async () => {
    if (!editingId || !editForm.name) return;
    
    const { error } = await supabase!
      .from('travelc_categories')
      .update({
        name: editForm.name,
        slug: editForm.slug,
        icon: editForm.icon,
        color: editForm.color,
        description: editForm.description,
        is_active: editForm.is_active
      })
      .eq('id', editingId);
    
    if (!error) {
      loadCategories();
      setEditingId(null);
      setEditForm({});
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm('Weet je zeker dat je deze categorie wilt verwijderen?')) return;
    
    const { error } = await supabase!
      .from('travelc_categories')
      .delete()
      .eq('id', id);
    
    if (!error) {
      loadCategories();
    }
  };

  const handleToggleActive = async (cat: Category) => {
    const { error } = await supabase!
      .from('travelc_categories')
      .update({ is_active: !cat.is_active })
      .eq('id', cat.id);
    
    if (!error) {
      loadCategories();
    }
  };

  if (loading) {
    return <div className="p-4 text-center text-gray-500">Laden...</div>;
  }

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h3 className="text-lg font-semibold">Reis Categorieën</h3>
        <button
          onClick={() => setIsAdding(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <Plus className="w-4 h-4" />
          Nieuwe Categorie
        </button>
      </div>

      {/* Add new category form */}
      {isAdding && (
        <div className="p-4 bg-blue-50 border border-blue-200 rounded-lg space-y-3">
          <div className="grid grid-cols-4 gap-3">
            <div>
              <label className="text-xs text-gray-500">Naam</label>
              <input
                type="text"
                value={newCategory.name}
                onChange={(e) => setNewCategory({ ...newCategory, name: e.target.value })}
                className="w-full px-3 py-2 border rounded-lg"
                placeholder="Categorie naam"
              />
            </div>
            <div>
              <label className="text-xs text-gray-500">Slug</label>
              <input
                type="text"
                value={newCategory.slug}
                onChange={(e) => setNewCategory({ ...newCategory, slug: e.target.value })}
                className="w-full px-3 py-2 border rounded-lg"
                placeholder="auto-generated"
              />
            </div>
            <div>
              <label className="text-xs text-gray-500">Icon (emoji)</label>
              <input
                type="text"
                value={newCategory.icon}
                onChange={(e) => setNewCategory({ ...newCategory, icon: e.target.value })}
                className="w-full px-3 py-2 border rounded-lg text-center text-xl"
              />
            </div>
            <div>
              <label className="text-xs text-gray-500">Kleur</label>
              <input
                type="color"
                value={newCategory.color}
                onChange={(e) => setNewCategory({ ...newCategory, color: e.target.value })}
                className="w-full h-10 border rounded-lg cursor-pointer"
              />
            </div>
          </div>
          <div className="flex gap-2">
            <button
              onClick={handleAdd}
              className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2"
            >
              <Save className="w-4 h-4" />
              Toevoegen
            </button>
            <button
              onClick={() => setIsAdding(false)}
              className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
            >
              Annuleren
            </button>
          </div>
        </div>
      )}

      {/* Categories list */}
      <div className="bg-white border rounded-lg divide-y">
        {categories.map((cat) => (
          <div key={cat.id} className={`p-3 flex items-center gap-4 ${!cat.is_active ? 'opacity-50' : ''}`}>
            <GripVertical className="w-4 h-4 text-gray-400 cursor-grab" />
            
            {editingId === cat.id ? (
              // Edit mode
              <>
                <input
                  type="text"
                  value={editForm.icon || ''}
                  onChange={(e) => setEditForm({ ...editForm, icon: e.target.value })}
                  className="w-12 px-2 py-1 border rounded text-center text-xl"
                />
                <input
                  type="text"
                  value={editForm.name || ''}
                  onChange={(e) => setEditForm({ ...editForm, name: e.target.value })}
                  className="flex-1 px-3 py-1 border rounded"
                />
                <input
                  type="text"
                  value={editForm.slug || ''}
                  onChange={(e) => setEditForm({ ...editForm, slug: e.target.value })}
                  className="w-32 px-3 py-1 border rounded text-sm text-gray-500"
                />
                <input
                  type="color"
                  value={editForm.color || '#3B82F6'}
                  onChange={(e) => setEditForm({ ...editForm, color: e.target.value })}
                  className="w-10 h-8 border rounded cursor-pointer"
                />
                <button onClick={handleSave} className="p-2 text-green-600 hover:bg-green-50 rounded">
                  <Save className="w-4 h-4" />
                </button>
                <button onClick={() => setEditingId(null)} className="p-2 text-gray-600 hover:bg-gray-50 rounded">
                  <X className="w-4 h-4" />
                </button>
              </>
            ) : (
              // View mode
              <>
                <span className="text-2xl w-10 text-center">{cat.icon}</span>
                <span className="flex-1 font-medium">{cat.name}</span>
                <span className="text-sm text-gray-400">{cat.slug}</span>
                <div 
                  className="w-6 h-6 rounded-full border"
                  style={{ backgroundColor: cat.color }}
                />
                <button
                  onClick={() => handleToggleActive(cat)}
                  className={`px-2 py-1 text-xs rounded ${cat.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}
                >
                  {cat.is_active ? 'Actief' : 'Inactief'}
                </button>
                <button onClick={() => handleEdit(cat)} className="p-2 text-blue-600 hover:bg-blue-50 rounded">
                  <Edit2 className="w-4 h-4" />
                </button>
                <button onClick={() => handleDelete(cat.id)} className="p-2 text-red-600 hover:bg-red-50 rounded">
                  <Trash2 className="w-4 h-4" />
                </button>
              </>
            )}
          </div>
        ))}
      </div>

      <p className="text-sm text-gray-500">
        💡 Deze categorieën worden gebruikt voor het filteren van reizen op de website en in WordPress.
      </p>
    </div>
  );
}
