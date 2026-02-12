import { useState, useEffect } from 'react';
import { supabase } from '../../lib/supabase';

interface Props {
  offerteId: string;
}

export function OfferteViewerSimple({ offerteId }: Props) {
  const [offerte, setOfferte] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function loadOfferte() {
      if (!supabase) {
        setError('Configuratiefout');
        setLoading(false);
        return;
      }
      try {
        const { data, error: dbError } = await supabase
          .from('travel_offertes')
          .select('*')
          .eq('id', offerteId)
          .single();

        if (dbError) throw dbError;
        if (!data) throw new Error('Offerte niet gevonden');

        setOfferte(data);
      } catch (err: any) {
        console.error('Error loading offerte:', err);
        setError(err.message || 'Kon offerte niet laden');
      } finally {
        setLoading(false);
      }
    }

    loadOfferte();
  }, [offerteId]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto mb-4"></div>
          <p className="text-gray-600">Offerte laden...</p>
        </div>
      </div>
    );
  }

  if (error || !offerte) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <h1 className="text-2xl font-bold text-gray-900 mb-2">Offerte niet gevonden</h1>
          <p className="text-gray-500">{error || 'Deze offerte bestaat niet.'}</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 p-8">
      <div className="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-4">{offerte.title}</h1>
        <p className="text-gray-600 mb-2">Voor: {offerte.client_name}</p>
        <p className="text-gray-600 mb-4">{offerte.subtitle}</p>
        
        {offerte.intro_text && (
          <div className="prose max-w-none mb-6">
            <p>{offerte.intro_text}</p>
          </div>
        )}

        <div className="mt-8 p-6 bg-orange-50 rounded-lg">
          <div className="text-2xl font-bold text-orange-600">
            €{offerte.total_price?.toFixed(2) || '0.00'}
          </div>
          <p className="text-sm text-gray-600 mt-1">
            Voor {offerte.number_of_travelers || 2} personen
          </p>
        </div>

        <div className="mt-8 text-sm text-gray-500">
          <p>Offerte ID: {offerte.id}</p>
          <p>Status: {offerte.status}</p>
        </div>
      </div>
    </div>
  );
}
