/*
 * Lokal: network data for the Mumbai suburban railway.
 *
 * Station order and distances (km from each corridor's first station) are
 * approximate. Timetables are GENERATED from service patterns below, not taken
 * from the official Western/Central Railway timetable. Replace PATTERNS (or the
 * whole generateTrips step in app.js) with real timetable data before relying
 * on it for travel.
 */

const LINES = {
  WR: { name: 'Western', short: 'WR' },
  CR: { name: 'Central', short: 'CR' },
  HR: { name: 'Harbour', short: 'HR' },
  TH: { name: 'Trans-Harbour', short: 'TH' },
};

// Each corridor is one physical route, listed in the "Down" direction
// (away from Churchgate / CSMT / Thane). [station name, km]
const CORRIDORS = {
  'WR-main': {
    line: 'WR',
    label: 'Churchgate – Dahanu Road',
    fast: ['Churchgate', 'Mumbai Central', 'Dadar', 'Bandra', 'Andheri', 'Borivali',
      'Bhayandar', 'Vasai Road', 'Nalla Sopara', 'Virar'],
    fastAllAfter: 'Virar',
    stations: [
      ['Churchgate', 0], ['Marine Lines', 1.3], ['Charni Road', 2.2], ['Grant Road', 3.3],
      ['Mumbai Central', 4.2], ['Mahalaxmi', 5.9], ['Lower Parel', 7.2], ['Prabhadevi', 8.0],
      ['Dadar', 9.6], ['Matunga Road', 10.7], ['Mahim Jn', 11.9], ['Bandra', 14.7],
      ['Khar Road', 16.0], ['Santacruz', 17.3], ['Vile Parle', 18.6], ['Andheri', 21.0],
      ['Jogeshwari', 22.9], ['Ram Mandir', 24.0], ['Goregaon', 25.6], ['Malad', 28.2],
      ['Kandivali', 30.2], ['Borivali', 32.2], ['Dahisar', 34.4], ['Mira Road', 38.2],
      ['Bhayandar', 40.3], ['Naigaon', 44.1], ['Vasai Road', 47.9], ['Nalla Sopara', 51.8],
      ['Virar', 55.9], ['Vaitarna', 66.1], ['Saphale', 74.0], ['Kelve Road', 81.9],
      ['Palghar', 89.6], ['Umroli', 95.5], ['Boisar', 101.9], ['Vangaon', 112.6],
      ['Dahanu Road', 123.7],
    ],
  },
  'CR-kasara': {
    line: 'CR',
    label: 'CSMT – Kalyan – Kasara',
    fast: ['CSMT', 'Byculla', 'Dadar', 'Ghatkopar', 'Vikhroli', 'Bhandup', 'Mulund',
      'Thane', 'Dombivli', 'Kalyan'],
    fastAllAfter: 'Kalyan',
    stations: [
      ['CSMT', 0], ['Masjid', 1.3], ['Sandhurst Road', 2.1], ['Byculla', 4.4],
      ['Chinchpokli', 5.5], ['Currey Road', 6.6], ['Parel', 7.8], ['Dadar', 9.0],
      ['Matunga', 10.1], ['Sion', 11.7], ['Kurla', 15.3], ['Vidyavihar', 17.0],
      ['Ghatkopar', 19.2], ['Vikhroli', 22.4], ['Kanjurmarg', 24.3], ['Bhandup', 26.4],
      ['Nahur', 27.6], ['Mulund', 29.2], ['Thane', 33.7], ['Kalwa', 35.8],
      ['Mumbra', 40.5], ['Diva', 42.2], ['Kopar', 46.0], ['Dombivli', 48.0],
      ['Thakurli', 49.6], ['Kalyan', 53.7], ['Shahad', 56.6], ['Ambivli', 59.3],
      ['Titwala', 63.8], ['Khadavli', 69.0], ['Vasind', 75.6], ['Asangaon', 85.8],
      ['Atgaon', 93.0], ['Khardi', 100.8], ['Umbermali', 109.5], ['Kasara', 120.6],
    ],
  },
  'CR-khopoli': {
    line: 'CR',
    label: 'CSMT – Kalyan – Khopoli',
    fast: ['CSMT', 'Byculla', 'Dadar', 'Ghatkopar', 'Vikhroli', 'Bhandup', 'Mulund',
      'Thane', 'Dombivli', 'Kalyan'],
    fastAllAfter: 'Kalyan',
    stations: [
      ['CSMT', 0], ['Masjid', 1.3], ['Sandhurst Road', 2.1], ['Byculla', 4.4],
      ['Chinchpokli', 5.5], ['Currey Road', 6.6], ['Parel', 7.8], ['Dadar', 9.0],
      ['Matunga', 10.1], ['Sion', 11.7], ['Kurla', 15.3], ['Vidyavihar', 17.0],
      ['Ghatkopar', 19.2], ['Vikhroli', 22.4], ['Kanjurmarg', 24.3], ['Bhandup', 26.4],
      ['Nahur', 27.6], ['Mulund', 29.2], ['Thane', 33.7], ['Kalwa', 35.8],
      ['Mumbra', 40.5], ['Diva', 42.2], ['Kopar', 46.0], ['Dombivli', 48.0],
      ['Thakurli', 49.6], ['Kalyan', 53.7], ['Vithalwadi', 55.2], ['Ulhasnagar', 56.8],
      ['Ambernath', 60.0], ['Badlapur', 67.9], ['Vangani', 75.9], ['Shelu', 79.9],
      ['Neral', 85.2], ['Bhivpuri Road', 90.0], ['Karjat', 100.5], ['Palasdhari', 102.7],
      ['Kelavli', 107.0], ['Dolavli', 109.7], ['Lowjee', 112.6], ['Khopoli', 114.5],
    ],
  },
  'HR-panvel': {
    line: 'HR',
    label: 'CSMT – Vashi – Panvel',
    stations: [
      ['CSMT', 0], ['Masjid', 1.3], ['Sandhurst Road', 2.1], ['Dockyard Road', 3.5],
      ['Reay Road', 4.3], ['Cotton Green', 5.3], ['Sewri', 6.8], ['Vadala Road', 8.5],
      ['GTB Nagar', 10.6], ['Chunabhatti', 11.4], ['Kurla', 13.2], ['Tilak Nagar', 14.3],
      ['Chembur', 15.5], ['Govandi', 17.4], ['Mankhurd', 19.5], ['Vashi', 23.6],
      ['Sanpada', 25.0], ['Juinagar', 27.0], ['Nerul', 29.1], ['Seawoods', 31.0],
      ['Belapur CBD', 34.0], ['Kharghar', 36.6], ['Mansarovar', 39.8],
      ['Khandeshwar', 44.0], ['Panvel', 48.9],
    ],
  },
  'HR-goregaon': {
    line: 'HR',
    label: 'CSMT – Bandra – Goregaon',
    stations: [
      ['CSMT', 0], ['Masjid', 1.3], ['Sandhurst Road', 2.1], ['Dockyard Road', 3.5],
      ['Reay Road', 4.3], ['Cotton Green', 5.3], ['Sewri', 6.8], ['Vadala Road', 8.5],
      ["King's Circle", 10.3], ['Mahim Jn', 12.2], ['Bandra', 15.0], ['Khar Road', 16.3],
      ['Santacruz', 17.6], ['Vile Parle', 18.9], ['Andheri', 21.3], ['Jogeshwari', 23.2],
      ['Ram Mandir', 24.3], ['Goregaon', 25.9],
    ],
  },
  'TH-vashi': {
    line: 'TH',
    label: 'Thane – Vashi',
    stations: [
      ['Thane', 0], ['Airoli', 5.0], ['Rabale', 8.0], ['Ghansoli', 11.0],
      ['Kopar Khairane', 13.5], ['Turbhe', 16.5], ['Sanpada', 19.0], ['Vashi', 21.0],
    ],
  },
  'TH-panvel': {
    line: 'TH',
    label: 'Thane – Nerul – Panvel',
    stations: [
      ['Thane', 0], ['Airoli', 5.0], ['Rabale', 8.0], ['Ghansoli', 11.0],
      ['Kopar Khairane', 13.5], ['Turbhe', 16.5], ['Juinagar', 20.5], ['Nerul', 22.6],
      ['Seawoods', 24.6], ['Belapur CBD', 27.6], ['Kharghar', 30.2], ['Mansarovar', 33.4],
      ['Khandeshwar', 37.6], ['Panvel', 42.5],
    ],
  },
};

// Service patterns. hw = [off-peak headway, peak headway] in minutes.
// Each pattern runs in both directions. ac = every Nth trip is an AC local.
// cars15 = every Nth trip is a 15-car rake.
const PATTERNS = [
  // Western
  { c: 'WR-main', from: 'Churchgate', to: 'Borivali', fast: false, hw: [12, 6] },
  { c: 'WR-main', from: 'Churchgate', to: 'Andheri', fast: false, hw: [24, 12] },
  { c: 'WR-main', from: 'Churchgate', to: 'Borivali', fast: true, hw: [20, 10], ac: 6 },
  { c: 'WR-main', from: 'Churchgate', to: 'Virar', fast: false, hw: [24, 14] },
  { c: 'WR-main', from: 'Churchgate', to: 'Virar', fast: true, hw: [15, 7], ac: 7, cars15: 4 },
  { c: 'WR-main', from: 'Churchgate', to: 'Dahanu Road', fast: true, hw: [150, 100] },
  { c: 'WR-main', from: 'Virar', to: 'Dahanu Road', fast: false, hw: [80, 50] },
  // Central
  { c: 'CR-kasara', from: 'CSMT', to: 'Thane', fast: false, hw: [20, 10] },
  { c: 'CR-kasara', from: 'CSMT', to: 'Kalyan', fast: false, hw: [15, 8] },
  { c: 'CR-kasara', from: 'CSMT', to: 'Kalyan', fast: true, hw: [16, 8], ac: 8, cars15: 5 },
  { c: 'CR-kasara', from: 'CSMT', to: 'Titwala', fast: false, hw: [45, 25] },
  { c: 'CR-kasara', from: 'CSMT', to: 'Kasara', fast: true, hw: [60, 35] },
  { c: 'CR-khopoli', from: 'CSMT', to: 'Ambernath', fast: false, hw: [35, 20] },
  { c: 'CR-khopoli', from: 'CSMT', to: 'Badlapur', fast: true, hw: [30, 15], ac: 6 },
  { c: 'CR-khopoli', from: 'CSMT', to: 'Karjat', fast: true, hw: [50, 30] },
  { c: 'CR-khopoli', from: 'Karjat', to: 'Khopoli', fast: false, hw: [100, 70] },
  // Harbour
  { c: 'HR-panvel', from: 'CSMT', to: 'Panvel', fast: false, hw: [15, 10] },
  { c: 'HR-panvel', from: 'CSMT', to: 'Vashi', fast: false, hw: [30, 20] },
  { c: 'HR-goregaon', from: 'CSMT', to: 'Goregaon', fast: false, hw: [30, 18] },
  { c: 'HR-goregaon', from: 'CSMT', to: 'Bandra', fast: false, hw: [60, 40] },
  // Trans-Harbour
  { c: 'TH-vashi', from: 'Thane', to: 'Vashi', fast: false, hw: [20, 12] },
  { c: 'TH-panvel', from: 'Thane', to: 'Panvel', fast: false, hw: [40, 25] },
];

// Common short codes people type into the search box.
const ALIASES = {
  'Churchgate': 'CCG', 'Mumbai Central': 'BCT', 'Dadar': 'DR DDR', 'Bandra': 'BA BDTS',
  'Andheri': 'ADH', 'Borivali': 'BVI BO', 'Virar': 'VR', 'Dahanu Road': 'DRD',
  'CSMT': 'VT CST Chhatrapati Shivaji Maharaj Terminus', 'Thane': 'TNA', 'Kalyan': 'KYN',
  'Kurla': 'CLA LTT', 'Panvel': 'PNVL', 'Vashi': 'VSH', 'Ghatkopar': 'GC',
  'Dombivli': 'DI', 'Mahim Jn': 'MM Mahim', 'Lower Parel': 'PL', 'Goregaon': 'GMN',
  'Malad': 'MDD', 'Kandivali': 'KILE', 'Bhayandar': 'BYR', 'Vasai Road': 'BSR Bassein',
  'Nalla Sopara': 'NSP', 'Mira Road': 'MIRA', 'Mulund': 'MLND', 'Karjat': 'KJT',
  'Kasara': 'KSRA', 'Khopoli': 'KHPI', 'Badlapur': 'BUD', 'Ambernath': 'ABH',
  'Belapur CBD': 'BEPR CBD Belapur', 'Vadala Road': 'VDLR Wadala', 'Prabhadevi': 'Elphinstone Road',
  'Byculla': 'BY', 'Sion': 'SIN', 'Chembur': 'CMBR', 'Nerul': 'NEU', 'Sandhurst Road': 'SNRD',
};

// Helplines shown on the More tab.
const HELPLINES = [
  { name: 'Rail Madad (complaints, security, medical)', number: '139' },
  { name: 'Railway Police (GRP) helpline', number: '1512' },
  { name: 'Women helpline', number: '1091' },
  { name: 'Emergency', number: '112' },
];
