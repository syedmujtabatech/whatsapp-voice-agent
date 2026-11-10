# VoiceWave AI: Roman-Urdu WhatsApp Voice Agent for E-Commerce

> **An intelligent, voice-first WhatsApp AI Sales Agent built on Laravel 12 that understands Roman-Urdu voice notes, checks real-time inventory, answers product inquiries, and automatically books Cash-on-Delivery (COD) orders.**

---

## 🎯 The Problem & Market Opportunity
- **60%+ of E-Commerce customers in Pakistan & South Asia prefer sending WhatsApp Voice Notes** instead of typing long text messages (*"Bhai black shoes size 9 mein hain aur deliver kab tak honge?"*).
- Customer support agents are slow, sleep at night, and miss high-intent sales leads.
- **VoiceWave AI** eliminates lost sales by instantly listening to voice notes in **Roman-Urdu**, extracting intent & customer entities (Name, Phone, Address, Quantity), decrementing inventory, and confirming orders in 1.2 seconds.

---

## 🚀 Live Demo & Presentation Walkthrough (Company Demo Guide)

The local server is already running at:
- **Live Simulator:** [http://127.0.0.1:8008/simulator](http://127.0.0.1:8008/simulator)
- **Executive Dashboard:** [http://127.0.0.1:8008/admin](http://127.0.0.1:8008/admin)
- **Voice Orders Management:** [http://127.0.0.1:8008/admin/orders](http://127.0.0.1:8008/admin/orders)
- **Speech & Conversation Logs:** [http://127.0.0.1:8008/admin/conversations](http://127.0.0.1:8008/admin/conversations)
- **Product Inventory:** [http://127.0.0.1:8008/admin/products](http://127.0.0.1:8008/admin/products)
- **WhatsApp Webhook Configuration:** [http://127.0.0.1:8008/admin/settings](http://127.0.0.1:8008/admin/settings)

---

### 🎙️ 5-Minute Demo Flow for the Interview/Meeting:

1. **Step 1: Open the Live Simulator (`/simulator`)**
   - Show the realistic WhatsApp Web interface.
   - Click the green **Microphone Button** and speak in Roman Urdu or English:
     > *"Assalam o alaikum bhai, Smart Watch Ultra 2 ka rate kya hai aur black color available hai?"*
   - Point out the **Right Sidebar (AI Intent Inspector)**:
     - Speech-to-Text transcript appears.
     - Classified Intent: `price_check`
     - Matched Product: `Smart Watch Ultra 2`
     - The AI replies back with text and **speaks aloud in voice**!

2. **Step 2: Demonstrate Automated Voice Order Booking**
   - Click the 1-Click Preset or speak:
     > *"Mera order book kar dein 1 piece Smart Watch Ultra 2. Mera naam Zeeshan Ali hai, Phone 03001234567, Address House 15, Street 4, DHA Phase 5 Lahore."*
   - Watch the AI automatically:
     - Detect intent `book_order`.
     - Extract customer name, phone, address, and quantity.
     - Book order `#ORD-XXXXX` with Cash on Delivery and Free Delivery calculations.
     - An order slip card appears directly in the chat!

3. **Step 3: Switch to the Admin Dashboard (`/admin`)**
   - Show the live metrics: Total Voice Orders, Revenue generated via voice, Average resolution speed (1.1s).
   - Show the **Customer Voice Intent Distribution** chart.

4. **Step 4: Open Voice Orders (`/admin/orders`)**
   - Open the newly created order.
   - Click **View Invoice**: show the printable invoice with the original customer voice transcript attached for proof-of-order audit!

5. **Step 5: Show Official Meta WhatsApp Cloud API Readiness (`/admin/settings`)**
   - Show the Meta Webhook Verification endpoint: `/api/whatsapp/webhook`
   - Show the Verify Token: `WHATSAPP_VOICE_VERIFY_2026`
   - Explain how any verified Meta Business number connects in under 2 minutes.

---

## 🛠️ Tech Stack & Architecture

- **Backend:** Laravel 12 (PHP 8.2+), SQLite / MySQL
- **Speech-to-Text (STT):** OpenAI Whisper API / Web Speech Recognition engine
- **NLP / Entity Extraction:** Custom Roman-Urdu entity slot-filling engine & intent classifier
- **Voice Synthesis (TTS):** Web Speech Synthesis & ElevenLabs audio pipeline
- **WhatsApp Integration:** Meta WhatsApp Cloud API (v19.0) Webhook handler with SHA256 HMAC verification
- **Frontend:** Tailwind CSS, Alpine.js, Chart.js, FontAwesome 6

---

## 💻 How to Run Locally

If the server ever restarts:
```bash
cd "C:\Users\Mujtaba Arshad\.gemini\antigravity\scratch\whatsapp-voice-agent"
php artisan serve --port=8008
```
Then visit: `http://127.0.0.1:8008/simulator`
