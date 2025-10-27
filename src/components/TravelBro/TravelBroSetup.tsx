import React, { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import { db } from '../../lib/supabase';
import { Bot, Phone, MessageSquare, Users, Settings, CheckCircle, XCircle, ExternalLink, Copy, Send } from 'lucide-react';

export function TravelBroSetup() {
  const { user } = useAuth();
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'overview' | 'setup' | 'sessions' | 'test'>('overview');
  const [apiSettings, setApiSettings] = useState<any>(null);
  const [whatsappSessions, setWhatsappSessions] = useState<any[]>([]);
  const [testPhone, setTestPhone] = useState('');
  const [testMessage, setTestMessage] = useState('Hoi! Dit is een test bericht van TravelBRO.');
  const [sendingTest, setSendingTest] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    setLoading(true);
    try {
      const { data: settings } = await db.supabase
        .from('api_settings')
        .select('*')
        .eq('brand_id', user?.brand_id)
        .maybeSingle();

      setApiSettings(settings);

      const { data: sessions } = await db.supabase
        .from('whatsapp_sessions')
        .select('*')
        .eq('brand_id', user?.brand_id)
        .order('created_at', { ascending: false });

      setWhatsappSessions(sessions || []);
    } catch (error) {
      console.error('Error loading data:', error);
    } finally {
      setLoading(false);
    }
  };

  const testTwilioConnection = async () => {
    if (!testPhone.trim()) {
      alert('Vul een telefoonnummer in');
      return;
    }

    setSendingTest(true);
    try {
      const response = await fetch(`${import.meta.env.VITE_SUPABASE_URL}/functions/v1/test-twilio`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${import.meta.env.VITE_SUPABASE_ANON_KEY}`,
        },
        body: JSON.stringify({
          to: testPhone,
          message: testMessage,
        }),
      });

      if (!response.ok) throw new Error('Test failed');

      const data = await response.json();
      alert('✅ Test bericht verzonden! Check je WhatsApp.');
    } catch (error) {
      console.error('Error testing Twilio:', error);
      alert('❌ Fout bij verzenden test bericht. Check de console voor details.');
    } finally {
      setSendingTest(false);
    }
  };

  const copyWebhookUrl = () => {
    const webhookUrl = `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/whatsapp-webhook`;
    navigator.clipboard.writeText(webhookUrl);
    alert('Webhook URL gekopieerd!');
  };

  if (loading) {
    return (
      <div className="p-6 flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600"></div>
      </div>
    );
  }

  const isTwilioConfigured = apiSettings?.twilio_account_sid && apiSettings?.twilio_auth_token;
  const webhookUrl = `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/whatsapp-webhook`;

  return (
    <div className="p-6">
      <div className="mb-6">
        <div className="flex items-center space-x-3 mb-2">
          <div className="w-12 h-12 rounded-lg flex items-center justify-center bg-gradient-to-br from-orange-500 to-amber-500">
            <Bot className="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">TravelBRO WhatsApp Assistent</h1>
            <p className="text-gray-600">Configureer en beheer je AI reisassistent</p>
          </div>
        </div>
      </div>

      <div className="border-b border-gray-200 mb-6">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', label: 'Overzicht', icon: Bot },
            { id: 'setup', label: 'Setup', icon: Settings },
            { id: 'sessions', label: 'Actieve Sessies', icon: Users },
            { id: 'test', label: 'Test', icon: Send },
          ].map((tab) => {
            const Icon = tab.icon;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id as any)}
                className={`flex items-center space-x-2 py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === tab.id
                    ? 'border-orange-500 text-orange-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`}
              >
                <Icon size={18} />
                <span>{tab.label}</span>
              </button>
            );
          })}
        </nav>
      </div>

      {activeTab === 'overview' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="bg-white rounded-lg shadow-sm border p-6">
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-semibold text-gray-900">Twilio Status</h3>
                {isTwilioConfigured ? (
                  <CheckCircle className="text-green-500" size={24} />
                ) : (
                  <XCircle className="text-red-500" size={24} />
                )}
              </div>
              <p className="text-sm text-gray-600">
                {isTwilioConfigured
                  ? 'Twilio is geconfigureerd en klaar voor gebruik'
                  : 'Twilio moet nog worden geconfigureerd'}
              </p>
            </div>

            <div className="bg-white rounded-lg shadow-sm border p-6">
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-semibold text-gray-900">Actieve Sessies</h3>
                <MessageSquare className="text-blue-500" size={24} />
              </div>
              <p className="text-3xl font-bold text-gray-900">{whatsappSessions.length}</p>
              <p className="text-sm text-gray-600">WhatsApp conversaties</p>
            </div>

            <div className="bg-white rounded-lg shadow-sm border p-6">
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-semibold text-gray-900">Webhook</h3>
                <Phone className="text-purple-500" size={24} />
              </div>
              <p className="text-sm text-gray-600 mb-2">Geconfigureerd in Twilio</p>
              <button
                onClick={copyWebhookUrl}
                className="text-sm text-orange-600 hover:text-orange-700 flex items-center space-x-1"
              >
                <Copy size={14} />
                <span>Kopieer URL</span>
              </button>
            </div>
          </div>

          <div className="bg-gradient-to-br from-orange-50 to-amber-50 rounded-lg border border-orange-200 p-6">
            <h3 className="font-semibold text-gray-900 mb-3">Hoe werkt TravelBRO?</h3>
            <ul className="space-y-2 text-sm text-gray-700">
              <li className="flex items-start space-x-2">
                <span className="text-orange-600 font-bold">1.</span>
                <span>Configureer Twilio WhatsApp in de Setup tab</span>
              </li>
              <li className="flex items-start space-x-2">
                <span className="text-orange-600 font-bold">2.</span>
                <span>Agents maken reizen aan en delen deze met klanten</span>
              </li>
              <li className="flex items-start space-x-2">
                <span className="text-orange-600 font-bold">3.</span>
                <span>Klanten chatten met TravelBRO via WhatsApp voor al hun reisvragen</span>
              </li>
              <li className="flex items-start space-x-2">
                <span className="text-orange-600 font-bold">4.</span>
                <span>AI beantwoordt vragen over bestemming, hotel, activiteiten en meer</span>
              </li>
            </ul>
          </div>
        </div>
      )}

      {activeTab === 'setup' && (
        <div className="space-y-6">
          <div className="bg-white rounded-lg shadow-sm border p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">Twilio WhatsApp Configuratie</h3>

            {isTwilioConfigured ? (
              <div className="space-y-4">
                <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                  <div className="flex items-center space-x-2 mb-2">
                    <CheckCircle className="text-green-600" size={20} />
                    <span className="font-medium text-green-900">Twilio is geconfigureerd</span>
                  </div>
                  <p className="text-sm text-green-700">
                    Account SID: {apiSettings.twilio_account_sid?.substring(0, 10)}...
                  </p>
                </div>

                <div className="border-t pt-4">
                  <h4 className="font-semibold text-gray-900 mb-3">Webhook Configuratie</h4>
                  <div className="bg-gray-50 rounded-lg p-4 mb-3">
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Webhook URL (kopieer deze naar Twilio)
                    </label>
                    <div className="flex space-x-2">
                      <input
                        type="text"
                        value={webhookUrl}
                        readOnly
                        className="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm"
                      />
                      <button
                        onClick={copyWebhookUrl}
                        className="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2"
                      >
                        <Copy size={16} />
                        <span>Kopieer</span>
                      </button>
                    </div>
                  </div>

                  <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p className="text-sm text-blue-800 font-medium mb-2">Configureer in Twilio:</p>
                    <ol className="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                      <li>Ga naar Twilio Console → Messaging → Settings</li>
                      <li>Plak de webhook URL bij "When a message comes in"</li>
                      <li>Selecteer "HTTP POST" als method</li>
                      <li>Sla op en test de verbinding</li>
                    </ol>
                  </div>
                </div>

                <button
                  onClick={() => window.open('https://console.twilio.com', '_blank')}
                  className="flex items-center space-x-2 text-orange-600 hover:text-orange-700"
                >
                  <ExternalLink size={16} />
                  <span>Open Twilio Console</span>
                </button>
              </div>
            ) : (
              <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p className="text-sm text-yellow-800">
                  Twilio is nog niet geconfigureerd. Vraag de operator om Twilio credentials toe te voegen in de API Settings.
                </p>
              </div>
            )}
          </div>
        </div>
      )}

      {activeTab === 'sessions' && (
        <div className="space-y-4">
          {whatsappSessions.length === 0 ? (
            <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
              <MessageSquare className="mx-auto h-12 w-12 text-gray-400 mb-4" />
              <h3 className="text-lg font-semibold text-gray-900 mb-2">Nog geen sessies</h3>
              <p className="text-gray-600">WhatsApp conversaties verschijnen hier zodra klanten beginnen te chatten</p>
            </div>
          ) : (
            <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Telefoon
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Reis
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Laatste activiteit
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {whatsappSessions.map((session) => (
                    <tr key={session.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {session.phone_number}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {session.trip_id ? 'Actieve reis' : 'Geen reis'}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                          session.is_active
                            ? 'bg-green-100 text-green-800'
                            : 'bg-gray-100 text-gray-800'
                        }`}>
                          {session.is_active ? 'Actief' : 'Inactief'}
                        </span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {new Date(session.last_activity).toLocaleString('nl-NL')}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      )}

      {activeTab === 'test' && (
        <div className="space-y-6">
          <div className="bg-white rounded-lg shadow-sm border p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">Test WhatsApp Bericht</h3>

            {isTwilioConfigured ? (
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Telefoonnummer (inclusief landcode, bijv: +31612345678)
                  </label>
                  <input
                    type="tel"
                    value={testPhone}
                    onChange={(e) => setTestPhone(e.target.value)}
                    placeholder="+31612345678"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Test Bericht
                  </label>
                  <textarea
                    value={testMessage}
                    onChange={(e) => setTestMessage(e.target.value)}
                    rows={4}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                  />
                </div>

                <button
                  onClick={testTwilioConnection}
                  disabled={sendingTest || !testPhone.trim()}
                  className="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                >
                  {sendingTest ? (
                    <>
                      <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                      <span>Verzenden...</span>
                    </>
                  ) : (
                    <>
                      <Send size={18} />
                      <span>Verstuur Test Bericht</span>
                    </>
                  )}
                </button>

                <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <p className="text-sm text-blue-800">
                    💡 Tip: Zorg dat je nummer eerst geautoriseerd is in je Twilio Sandbox voor WhatsApp tests.
                  </p>
                </div>
              </div>
            ) : (
              <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p className="text-sm text-yellow-800">
                  Configureer eerst Twilio in de Setup tab voordat je test berichten kunt versturen.
                </p>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
