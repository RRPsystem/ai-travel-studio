import { FileText, Plus, Search, Filter } from 'lucide-react';

export function TravelDocsOffertes() {
  return (
    <div className="p-6">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-2xl font-bold text-gray-900 flex items-center gap-3">
              <div className="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <FileText className="w-5 h-5 text-blue-600" />
              </div>
              Offertes
            </h1>
            <p className="text-gray-500 mt-1">Maak professionele reisoffertes voor je klanten</p>
          </div>
          <button className="flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors shadow-sm">
            <Plus size={18} />
            Nieuwe Offerte
          </button>
        </div>

        {/* Search & Filter Bar */}
        <div className="flex items-center gap-3 mb-6">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              placeholder="Zoek offertes op klantnaam, bestemming..."
              className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-200 focus:border-orange-400 outline-none text-sm"
            />
          </div>
          <button className="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 text-sm">
            <Filter size={16} />
            Filter
          </button>
        </div>

        {/* Empty State */}
        <div className="bg-white rounded-2xl border border-gray-200 shadow-sm">
          <div className="flex flex-col items-center justify-center py-20 px-6 text-center">
            <div className="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mb-6">
              <FileText className="w-10 h-10 text-blue-400" />
            </div>
            <h3 className="text-xl font-semibold text-gray-900 mb-2">Nog geen offertes</h3>
            <p className="text-gray-500 max-w-md mb-6">
              Maak je eerste professionele reisofferte. Kies een reis, personaliseer de inhoud en verstuur direct naar je klant.
            </p>
            <button className="flex items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-medium transition-colors">
              <Plus size={18} />
              Maak je eerste offerte
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
