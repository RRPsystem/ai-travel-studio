import React, { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { db } from '../../lib/supabase';
import { generateBuilderJWT } from '../../lib/jwtHelper';
import { FileText, Plus, Edit, Trash2, ExternalLink } from 'lucide-react';

export function PageManagement() {
  const { user } = useAuth();
  const [pages, setPages] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [formData, setFormData] = useState({
    title: '',
    slug: '',
  });

  useEffect(() => {
    if (user?.brand_id) {
      loadPages();
    }
  }, [user?.brand_id]);

  const loadPages = async () => {
    if (!user?.brand_id) return;
    setLoading(true);
    try {
      const { data, error } = await db.supabase
        .from('pages')
        .select('*')
        .eq('brand_id', user.brand_id)
        .eq('is_template', false)
        .order('created_at', { ascending: false });

      if (error) throw error;
      setPages(data || []);
    } catch (error) {
      console.error('Error loading pages:', error);
      alert('Fout bij laden van pagina\'s');
    } finally {
      setLoading(false);
    }
  };

  const handleCreatePage = async () => {
    if (!user) return;

    try {
      const jwtResponse = await generateBuilderJWT(
        'brand',
        user.id,
        ['pages:write', 'content:write', 'layouts:write', 'menus:write'],
        {
          forceBrandId: user.brand_id,
          mode: 'create-page',
        }
      );

      const params = new URLSearchParams({
        api: `${import.meta.env.VITE_SUPABASE_URL}/functions/v1`,
        token: jwtResponse.token,
        apikey: import.meta.env.VITE_SUPABASE_ANON_KEY,
        brand_id: user.brand_id,
        mode: 'create-page',
        content_type: 'page',
        title: formData.title,
        slug: formData.slug,
      });

      const url = `https://www.ai-websitestudio.nl/?${params.toString()}#/mode/page`;
      window.open(url, '_blank');

      setShowCreateForm(false);
      setFormData({ title: '', slug: '' });

      setTimeout(() => {
        loadPages();
      }, 2000);
    } catch (error) {
      console.error('Error creating page:', error);
      alert('Er is een fout opgetreden bij het aanmaken van de pagina');
    }
  };

  const handleEditPage = async (pageId: string) => {
    if (!user) return;

    try {
      const jwtResponse = await generateBuilderJWT(
        'brand',
        user.id,
        ['pages:write', 'content:write', 'layouts:write', 'menus:write'],
        {
          pageId: pageId,
          forceBrandId: user.brand_id,
          mode: 'edit-page',
        }
      );

      const params = new URLSearchParams({
        api: `${import.meta.env.VITE_SUPABASE_URL}/functions/v1`,
        token: jwtResponse.token,
        apikey: import.meta.env.VITE_SUPABASE_ANON_KEY,
        brand_id: user.brand_id,
        page_id: pageId,
        mode: 'edit-page',
        content_type: 'page',
      });

      const url = `https://www.ai-websitestudio.nl/?${params.toString()}#/mode/page`;
      window.open(url, '_blank');
    } catch (error) {
      console.error('Error editing page:', error);
      alert('Er is een fout opgetreden bij het bewerken van de pagina');
    }
  };

  const handleDeletePage = async (pageId: string) => {
    if (!confirm('Weet je zeker dat je deze pagina wilt verwijderen?')) return;

    try {
      const { error } = await db.supabase
        .from('pages')
        .delete()
        .eq('id', pageId);

      if (error) throw error;

      alert('Pagina succesvol verwijderd');
      loadPages();
    } catch (error) {
      console.error('Error deleting page:', error);
      alert('Er is een fout opgetreden bij het verwijderen van de pagina');
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-gray-600">Laden...</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Pagina Beheer</h2>
          <p className="text-gray-600">Beheer alle pagina's van je website</p>
        </div>
        <button
          onClick={() => setShowCreateForm(!showCreateForm)}
          className="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2"
        >
          <Plus size={20} />
          <span>Nieuwe Pagina</span>
        </button>
      </div>

      {showCreateForm && (
        <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 space-y-4">
          <h3 className="text-lg font-semibold text-gray-900">Nieuwe Pagina Aanmaken</h3>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Titel</label>
              <input
                type="text"
                value={formData.title}
                onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                placeholder="bijv: Over Ons"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
              <input
                type="text"
                value={formData.slug}
                onChange={(e) => setFormData({ ...formData, slug: e.target.value.toLowerCase().replace(/\s+/g, '-') })}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                placeholder="bijv: over-ons"
              />
            </div>
          </div>
          <div className="flex space-x-2">
            <button
              onClick={handleCreatePage}
              disabled={!formData.title || !formData.slug}
              className="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Open Builder & Maak Pagina
            </button>
            <button
              onClick={() => setShowCreateForm(false)}
              className="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors"
            >
              Annuleren
            </button>
          </div>
        </div>
      )}

      {pages.length === 0 ? (
        <div className="text-center py-12">
          <FileText className="h-16 w-16 text-gray-400 mx-auto mb-4" />
          <h3 className="text-lg font-semibold text-gray-900 mb-2">Geen pagina's gevonden</h3>
          <p className="text-gray-600">Maak je eerste pagina om te beginnen</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {pages.map((page) => (
            <div key={page.id} className="bg-white border rounded-lg overflow-hidden hover:shadow-md transition-shadow">
              <div className="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                <FileText className="h-16 w-16 text-gray-400" />
              </div>

              <div className="p-4">
                <div className="mb-2">
                  <h3 className="font-semibold text-gray-900">{page.title}</h3>
                  <p className="text-sm text-gray-600">/{page.slug}</p>
                  <p className="text-xs text-gray-500 mt-1">
                    {new Date(page.created_at).toLocaleDateString('nl-NL')}
                  </p>
                </div>

                <div className="flex space-x-2">
                  <button
                    onClick={() => handleEditPage(page.id)}
                    className="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2"
                  >
                    <Edit size={16} />
                    <span>Bewerken</span>
                  </button>
                  <button
                    onClick={() => handleDeletePage(page.id)}
                    className="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition-colors"
                  >
                    <Trash2 size={16} />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
