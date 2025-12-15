import React, { useState, useEffect } from 'react';
import { supabase } from '../../lib/supabase';
import { Plus, Clock, Mic, Radio, Music } from 'lucide-react';

interface Segment {
  id: string;
  segment_type: string;
  title: string;
  duration_minutes: number | null;
  order_index: number;
  is_recorded: boolean;
}

interface EpisodeTimelineProps {
  episodeId: string;
  totalDuration: number;
  onDurationChange: (minutes: number) => void;
  onStatsUpdate: () => void;
}

export default function EpisodeTimeline({ episodeId, totalDuration, onDurationChange, onStatsUpdate }: EpisodeTimelineProps) {
  const [segments, setSegments] = useState<Segment[]>([]);
  const [showAddForm, setShowAddForm] = useState(false);
  const [newSegment, setNewSegment] = useState({
    segment_type: 'main',
    title: '',
    duration_minutes: 10
  });

  useEffect(() => {
    loadSegments();
  }, [episodeId]);

  useEffect(() => {
    const total = segments.reduce((sum, s) => sum + (s.duration_minutes || 0), 0);
    onDurationChange(total);
  }, [segments]);

  const loadSegments = async () => {
    const { data } = await supabase
      .from('podcast_segments')
      .select('*')
      .eq('episode_planning_id', episodeId)
      .order('order_index', { ascending: true });

    setSegments(data || []);
  };

  const addSegment = async () => {
    if (!newSegment.title.trim()) return;

    const maxOrder = Math.max(...segments.map(s => s.order_index), 0);

    const { error } = await supabase
      .from('podcast_segments')
      .insert({
        episode_planning_id: episodeId,
        ...newSegment,
        order_index: maxOrder + 1,
        is_recorded: false
      });

    if (!error) {
      setNewSegment({ segment_type: 'main', title: '', duration_minutes: 10 });
      setShowAddForm(false);
      loadSegments();
      onStatsUpdate();
    }
  };

  const getSegmentIcon = (type: string) => {
    switch (type) {
      case 'intro': return <Music size={16} />;
      case 'interview': return <Mic size={16} />;
      case 'outro': return <Music size={16} />;
      case 'ad_break': return <Radio size={16} />;
      default: return <Clock size={16} />;
    }
  };

  return (
    <div className="max-w-5xl mx-auto">
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Episode Timeline</h2>
          <p className="text-gray-600">Totale duur: {totalDuration} minuten</p>
        </div>
        <button
          onClick={() => setShowAddForm(!showAddForm)}
          className="flex items-center space-x-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700"
        >
          <Plus size={18} />
          <span>Segment Toevoegen</span>
        </button>
      </div>

      {showAddForm && (
        <div className="bg-white rounded-lg border border-gray-200 p-6 mb-6">
          <h3 className="font-semibold text-gray-900 mb-4">Nieuw Segment</h3>
          <div className="grid grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Type</label>
              <select
                value={newSegment.segment_type}
                onChange={(e) => setNewSegment({ ...newSegment, segment_type: e.target.value })}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
              >
                <option value="intro">Intro</option>
                <option value="main">Hoofdonderdeel</option>
                <option value="interview">Interview</option>
                <option value="ad_break">Reclameblok</option>
                <option value="outro">Outro</option>
                <option value="pre_recorded">Vooraf Opgenomen</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Titel</label>
              <input
                type="text"
                value={newSegment.title}
                onChange={(e) => setNewSegment({ ...newSegment, title: e.target.value })}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Duur (min)</label>
              <input
                type="number"
                value={newSegment.duration_minutes}
                onChange={(e) => setNewSegment({ ...newSegment, duration_minutes: parseInt(e.target.value) })}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
              />
            </div>
          </div>
          <div className="mt-4 flex space-x-2">
            <button onClick={addSegment} className="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
              Segment Toevoegen
            </button>
            <button onClick={() => setShowAddForm(false)} className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
              Annuleren
            </button>
          </div>
        </div>
      )}

      <div className="bg-white rounded-lg border border-gray-200 p-6">
        {segments.length === 0 ? (
          <div className="text-center py-12 text-gray-500">Nog geen segmenten toegevoegd</div>
        ) : (
          <div className="space-y-2">
            {segments.map((segment, index) => (
              <div key={segment.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div className="flex items-center space-x-4">
                  <span className="text-sm font-medium text-gray-500">#{index + 1}</span>
                  <div className="text-orange-600">{getSegmentIcon(segment.segment_type)}</div>
                  <div>
                    <p className="font-medium text-gray-900">{segment.title}</p>
                    <p className="text-sm text-gray-600 capitalize">{segment.segment_type.replace('_', ' ')}</p>
                  </div>
                </div>
                <div className="flex items-center space-x-4">
                  <span className="text-sm text-gray-600">{segment.duration_minutes} min</span>
                  {segment.is_recorded && (
                    <span className="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Opgenomen</span>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
