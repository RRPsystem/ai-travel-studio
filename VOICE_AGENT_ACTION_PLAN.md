# 🎙️ TravelAgent Voice - Action Plan

## Status: Planning → Execution

**Huidige situatie:** Alleen documentatie, geen code
**Doel:** Werkende voice-to-offer systeem voor travel agents
**Tijdlijn:** 12 weken (3 maanden)

---

## 🚨 Kritieke Beslissingen (EERST)

### Beslissing 1: Externe Builder - JA of NEE?

**Optie A: Met externe builder (volgens originele plan)**
- ✅ Sneller time-to-market (specialist doet zijn ding)
- ✅ Betere kwaliteit offer pages (hun expertise)
- ✅ Minder technische schuld voor ons
- ❌ Afhankelijkheid van externe partij
- ❌ Kosten per offer (€2-3)
- ❌ Minder controle over kwaliteit/speed

**Optie B: Volledig intern bouwen**
- ✅ Volledige controle
- ✅ Geen externe kosten
- ✅ Snellere iteraties
- ❌ Langere ontwikkeltijd (meer te bouwen)
- ❌ Wij moeten hotel/flight API's integreren
- ❌ Wij moeten templates ontwerpen

**ACTIE:** Kies binnen 3 dagen

---

### Beslissing 2: Mobile App - ECHT of PWA?

**Optie A: Native apps (iOS + Android)**
- ✅ Beste performance
- ✅ Push notifications makkelijker
- ✅ Better voice integration
- ✅ App Store presence
- ❌ 2x development (iOS en Android)
- ❌ App Store review proces
- ❌ Updates trager

**Optie B: Progressive Web App (PWA)**
- ✅ 1x bouwen, overal werken
- ✅ Instant updates
- ✅ Geen app store goedkeuring nodig
- ❌ Voice API beperkingen in browser
- ❌ Minder "native" gevoel
- ❌ Push notifications complexer

**Optie C: React Native (hybrid)**
- ✅ 1x codebase voor iOS + Android
- ✅ Native performance
- ✅ Groot ecosystem
- ❌ Extra framework om te leren
- ❌ Platform-specifieke bugs

**ACTIE:** Kies binnen 3 dagen

---

### Beslissing 3: Voice Engine - Welke?

**Optie A: OpenAI Realtime API (in de docs genoemd)**
- ✅ State-of-the-art kwaliteit
- ✅ Natuurlijke conversaties
- ✅ Multi-turn dialogen
- ❌ Duur ($0.06/min audio)
- ❌ Amerikaans bedrijf (privacy?)
- ❌ Beta/experimental nog

**Optie B: Whisper (OpenAI) + GPT + TTS**
- ✅ Stabiele APIs
- ✅ Goedkoper
- ✅ Meer controle
- ❌ Minder natuurlijk (niet real-time)
- ❌ Hogere latency
- ❌ 3 API calls per turn

**Optie C: Google Cloud Speech + Dialogflow**
- ✅ Europese servers mogelijk
- ✅ Betere Nederlands support
- ✅ Enterprise-ready
- ❌ Minder geavanceerd dan OpenAI
- ❌ Complexere setup

**ACTIE:** Kies binnen 3 dagen

---

## 📋 Implementatie Plan

### FASE 0: Foundations (Week 1-2)

**Beslissingen finaliseren:**
- [ ] Externe builder ja/nee
- [ ] Mobile platform keuze
- [ ] Voice engine keuze
- [ ] Pricing model bepalen (voor agents)
- [ ] Beta testers identificeren (5 agencies)

**Database ontwerp:**
- [ ] `voice_conversations` tabel (opslaan voice sessies)
- [ ] `voice_offers` tabel (gegenereerde offers)
- [ ] `voice_agents` tabel (travel agents met voice toegang)
- [ ] `offer_generation_jobs` tabel (job queue)
- [ ] `offer_templates` tabel (template configuratie)

**Infrastructuur:**
- [ ] Supabase Edge Functions voor voice processing
- [ ] Audio storage bucket (voor opnames)
- [ ] Webhook endpoints opzetten
- [ ] Rate limiting configureren

---

### FASE 1: Voice Interface (Week 3-6)

**Als je INTERN bouwt:**

#### 1.1 Speech-to-Text Integration
- [ ] OpenAI Whisper API integratie
- [ ] Audio upload naar Supabase Storage
- [ ] Transcriptie processing
- [ ] Error handling (ruis, accent, etc.)

#### 1.2 Conversation Management
- [ ] Intent detection (wat wil de agent?)
- [ ] Entity extraction (bestemming, datum, budget, etc.)
- [ ] Context tracking (multi-turn conversation)
- [ ] Clarifying questions generator

#### 1.3 Text-to-Speech
- [ ] OpenAI TTS of Google Cloud TTS
- [ ] Voice selection (Nederlands accent)
- [ ] Audio streaming
- [ ] Caching van veelgebruikte zinnen

#### 1.4 Conversation AI
```
Agent zegt: "Las Vegas, 15-22 juni, 2 volwassenen, 4-ster hotel met casino"

Systeem moet:
1. Herkennen: destinatie, datums, travelers, voorkeuren
2. Checken: wat ontbreekt? (vertrekpunt, budget, template)
3. Vragen: "Vanaf welk vliegveld vertrekken jullie?"
4. Onthouden: alle eerder gegeven info
5. Bevestigen: "Oké, dus Las Vegas vanaf Amsterdam, 15-22 juni..."
```

**Edge Functions te bouwen:**
- [ ] `voice-intake/index.ts` - Audio ontvangen
- [ ] `voice-transcribe/index.ts` - STT
- [ ] `voice-process/index.ts` - Intent + entities
- [ ] `voice-respond/index.ts` - Generate response + TTS
- [ ] `voice-session/index.ts` - Session management

---

### FASE 2: Offer Generation (Week 6-8)

**Als je externe builder HEBT:**

#### 2.1 API Integration
- [ ] Implementeer POST /v1/offers/create endpoint call
- [ ] Webhook ontvangst systeem
- [ ] Status polling (optioneel)
- [ ] Error handling + retry logic
- [ ] Timeout handling (als offer te lang duurt)

**Edge Functions:**
- [ ] `create-external-offer/index.ts` - Call externe API
- [ ] `offer-webhook-handler/index.ts` - Ontvang completion
- [ ] `offer-status-checker/index.ts` - Poll status

---

**Als je INTERN bouwt (veel meer werk!):**

#### 2.2 Hotel Search
- [ ] Booking.com API integratie (of alternatief)
- [ ] Prijs vergelijking
- [ ] Beschikbaarheid check
- [ ] Image scraping/API
- [ ] Reviews aggregatie

#### 2.3 Flight Search
- [ ] Amadeus API of Skyscanner API
- [ ] Multi-city search
- [ ] Price comparison
- [ ] Availability check

#### 2.4 Content Enrichment
- [ ] YouTube API voor destination videos
- [ ] Google Places voor hotel details
- [ ] Trip Advisor voor reviews
- [ ] Stock photos voor destinations

#### 2.5 Template Engine
- [ ] HTML/CSS templates (3 stijlen)
- [ ] Dynamic data injection
- [ ] Responsive design
- [ ] PDF generation (optioneel)

**Edge Functions:**
- [ ] `search-hotels/index.ts`
- [ ] `search-flights/index.ts`
- [ ] `generate-offer-page/index.ts`
- [ ] `render-template/index.ts`

---

### FASE 3: Mobile App (Week 8-10)

**UI Components:**
- [ ] Voice recording button (hold-to-talk)
- [ ] Waveform visualisatie tijdens opname
- [ ] Live transcript weergave
- [ ] AI response display (tekst + audio)
- [ ] Offer preview card
- [ ] Share functionaliteit

**Flows:**
```
1. Login → Voice Dashboard
2. Tap "New Offer" → Voice Recording
3. Speak → See transcript → AI responds
4. Conversation completes → "Generating offer..."
5. Push notification → "Offer ready!"
6. View offer → Share with client (WhatsApp)
```

**Features:**
- [ ] Background recording (als app niet in focus)
- [ ] Offline queueing (save audio, upload later)
- [ ] History (previous offers)
- [ ] Templates selectie (A/B/C)
- [ ] Client contact management
- [ ] Push notifications

**Tech Stack beslissing:**
- Native: Swift + Kotlin
- React Native: JavaScript
- PWA: React + Web APIs

---

### FASE 4: Notifications & Sharing (Week 10-11)

#### 4.1 Push Notifications
- [ ] FCM (Firebase Cloud Messaging) setup
- [ ] iOS APNs setup
- [ ] Notification triggers:
  - "Offer ready for [Client Name]"
  - "Your offer expires in 24 hours"
  - "Client viewed your offer"
  - "Client selected option X"

#### 4.2 Sharing Systeem
- [ ] Short URL generator (rbj.nl/o/abc123)
- [ ] QR code generator
- [ ] WhatsApp share deep link
- [ ] Email share template
- [ ] SMS share optie

#### 4.3 Analytics
- [ ] Track offer views
- [ ] Track option selections
- [ ] Track conversion rate
- [ ] Track time-to-offer
- [ ] Track agent satisfaction

**Edge Functions:**
- [ ] `send-notification/index.ts`
- [ ] `generate-short-url/index.ts`
- [ ] `track-offer-view/index.ts`

---

### FASE 5: Testing & Polish (Week 11-12)

#### 5.1 Beta Testing
- [ ] 5 agencies onboarden
- [ ] Training sessies geven
- [ ] Real offers maken
- [ ] Feedback verzamelen
- [ ] Bugs fixen

#### 5.2 Performance
- [ ] Offer generation < 3 minuten (meten)
- [ ] Voice latency < 2 seconden (meten)
- [ ] App load time < 1 seconde
- [ ] Template render < 2 seconden

#### 5.3 Kwaliteit
- [ ] Voice accuracy > 95%
- [ ] Intent detection > 90%
- [ ] Hotel relevantie check
- [ ] Pricing accuracy check
- [ ] Template responsiveness test

#### 5.4 Security
- [ ] Audio data encryptie
- [ ] API key rotation
- [ ] Rate limiting testen
- [ ] RLS policies checken

---

## 📊 Resource Planning

### Als je EXTERN bouwt:

**Development team nodig:**
- 1x Backend developer (voice + webhooks) - **6 weken**
- 1x Mobile developer (iOS of Android) - **8 weken**
- 1x Designer (UI/UX) - **2 weken**

**Kosten:**
- Development: ~€40.000 (3x developer @ €5k/week)
- OpenAI API: ~€500/maand (100 agents × 10 offers × €0.05)
- Externe builder: €2.50 × 1000 offers/maand = €2.500/maand
- Supabase: ~€100/maand
- **Total first 3 months: ~€50.000**

---

### Als je INTERN bouwt:

**Development team nodig:**
- 1x Backend developer (voice + offer generation) - **10 weken**
- 1x Integration specialist (hotel/flight APIs) - **8 weken**
- 1x Frontend/Mobile developer - **8 weken**
- 1x Designer (templates + UI) - **4 weken**

**Kosten:**
- Development: ~€70.000 (4x developers)
- OpenAI API: ~€500/maand
- Hotel/Flight APIs: ~€1.000/maand
- Supabase: ~€100/maand
- **Total first 3 months: ~€75.000**

**Maar daarna:**
- Geen €2.50 per offer
- Volledige controle
- Snellere iteraties

---

## 🎯 Milestones

### Week 2: Beslissingen Round
- ✅ Alle tech keuzes gemaakt
- ✅ Beta testers gecommitteerd
- ✅ Database schema klaar

### Week 6: Voice Prototype
- ✅ Agent kan spraak-naar-tekst doen
- ✅ Systeem herkent intent + entities
- ✅ Conversatie flow werkt
- ✅ Demo-able voor stakeholders

### Week 8: Offer Generation Werkt
- ✅ Van voice → structured data → offer URL
- ✅ Template(s) zien er goed uit
- ✅ Mobiele pagina responsive

### Week 10: End-to-End Demo
- ✅ Complete flow werkt
- ✅ Agent kan echt offer maken
- ✅ Client kan offer bekijken
- ✅ Notificaties werken

### Week 12: Beta Launch
- ✅ 5 agencies live
- ✅ 50+ echte offers gemaakt
- ✅ Feedback verzameld
- ✅ Ready for scale

---

## 🚧 Risks & Dependencies

### HIGH RISK:
1. **Voice accuracy Nederlands** - OpenAI is vooral Engels getraind
   - *Mitigation:* Uitgebreid testen, fallback naar Engels

2. **Externe builder levert niet op tijd** - Als je externe route gaat
   - *Mitigation:* SLA in contract, penalty clauses, backup plan

3. **Offer generation te traag** - > 3 minuten = bad UX
   - *Mitigation:* Caching, pre-fetching, parallel API calls

4. **Agents adopteren het niet** - Voice is te onwennig
   - *Mitigation:* Ook text-input optie, gradual rollout

### MEDIUM RISK:
5. **Hotel/Flight API kosten exploderen** - Bij veel volume
   - *Mitigation:* Caching, rate limiting, pricing tiers

6. **Mobile app review rejection** - Apple/Google zeggen nee
   - *Mitigation:* PWA backup, compliance check vooraf

### LOW RISK:
7. **Notificaties niet betrouwbaar** - Push notifications falen
   - *Mitigation:* Email + SMS backup

---

## 📞 Next Actions (Deze Week!)

### Actie 1: Stakeholder Meeting
**Wie:** Product owner, CTO, potentiële beta testers
**Doel:** Beslissingen 1-3 nemen
**Duur:** 2 uur
**Output:** Go/no-go op externe builder, platform keuze, voice engine

### Actie 2: Externe Builder Outreach
**Als jullie externe route kiezen:**
- [ ] Shortlist maken (3-5 kandidaten)
- [ ] VOICE_AGENT_EXTERNAL_BUILDER_BRIEFING.md sturen
- [ ] Calls inplannen
- [ ] Proposals opvragen
- [ ] Keuze maken binnen 2 weken

### Actie 3: Budget Goedkeuring
- [ ] Present dit plan aan finance
- [ ] Budget request: €50K (extern) of €75K (intern)
- [ ] Goedkeuring binnen 1 week

### Actie 4: Beta Testers Rekruteren
- [ ] Shortlist 10 agencies
- [ ] Pitch meeting inplannen
- [ ] Committment krijgen van 5
- [ ] NDA + beta agreement

### Actie 5: Tech Spike (1 week)
- [ ] OpenAI Realtime API testen (proof of concept)
- [ ] Voice recording in browser/app testen
- [ ] Nederlands accent test
- [ ] Latency meten

---

## 💰 Business Model (Quick Recap)

### Voor Travel Agents:
- **€299/maand** per agent (unlimited offers)
- Of: **€99/maand** + €2 per offer
- Of: **Freemium** (5 offers gratis, dan betalen)

### ROI voor Agent:
- Oud: 4 offers/dag × €2500 avg = €10K/dag
- Nieuw: 12 offers/dag × €2500 avg = €30K/dag
- **Extra revenue: €20K/dag = €400K/maand**
- App kost €299/maand
- **ROI: 1333x** 🤯

### Voor Ons:
- **Year 1 target:** 100 agents × €299 = €29.9K/maand = **€358K/jaar**
- **Year 2 target:** 500 agents = **€1.79M/jaar**
- **Year 3 target:** 2000 agents = **€7.16M/jaar**

---

## 📅 Timeline Visual

```
Week 1-2:   [BESLISSINGEN] → Kies tech stack
Week 3-6:   [VOICE] → Spraak-naar-tekst + AI conversatie
Week 6-8:   [OFFERS] → Offer generation integratie
Week 8-10:  [MOBILE] → App bouwen + UI polish
Week 10-11: [NOTIFICATIONS] → Push + sharing
Week 11-12: [BETA] → 5 agencies live
Week 13+:   [SCALE] → Open for 100+ agencies
```

---

## ✅ Definition of Done

**Dit project is KLAAR als:**

1. ✅ Agent kan via voice een offer aanvragen (Nederlands)
2. ✅ Systeem genereert offer in < 3 minuten
3. ✅ Offer ziet er professioneel uit (mobile-first)
4. ✅ Agent krijgt notificatie als offer klaar is
5. ✅ Agent kan offer delen via WhatsApp
6. ✅ Client kan offer bekijken op mobiel
7. ✅ 5 beta agencies gebruiken het dagelijks
8. ✅ 100+ real offers gemaakt en positieve feedback
9. ✅ System uptime > 99%
10. ✅ Ready to scale naar 100+ agencies

---

## 🤔 Open Questions

1. **Wie is onze target externe builder?** (naam/bedrijf?)
2. **Hebben we al prototype voice UI designs?**
3. **Wie wordt product owner van dit project?**
4. **Wat is ons launch marketing plan?**
5. **Gaan we dit eerst intern testen voor eigen GoWild reizen?**
6. **Hoe verhouden dit zich tot bestaande TravelBro WhatsApp bot?**
7. **Kunnen we TravelBro tech hergebruiken?** (heeft al trip generation logica)

---

## 📎 Related Documents

- `VOICE_AGENT_EXTERNAL_BUILDER_BRIEFING.md` - Tech specs voor builder
- `VOICE_AGENT_PITCH_DECK.md` - Sales pitch & vision
- `TRAVELBRO_V2_UPGRADE.md` - Huidige WhatsApp trip bot (mogelijk te hergebruiken)

---

**Status:** Wachtend op go-beslissing
**Next:** Stakeholder meeting deze week
**Owner:** TBD

---

*Gemaakt: 8 januari 2026*
*Laatste update: 8 januari 2026*
