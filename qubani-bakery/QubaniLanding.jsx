import React, { useEffect, useRef, useState } from "react";
import {
  ArrowRight,
  Award,
  Check,
  ChevronDown,
  Clock,
  Crown,
  Droplets,
  Flame,
  Gift,
  Heart,
  Hotel,
  PartyPopper,
  Instagram,
  Leaf,
  MapPin,
  Menu,
  MessageCircle,
  Snowflake,
  Minus,
  Phone,
  Plus,
  Quote,
  ShieldCheck,
  Sparkles,
  Star,
  Truck,
  Users,
  X,
} from "lucide-react";

/* ------------------------------------------------------------------ */
/*  Config — change these to your own details                          */
/* ------------------------------------------------------------------ */

const BRAND = "Apricot Dessert";
const WHATSAPP_NUMBER = "918408820830"; // country code + number, digits only
const PHONE_DISPLAY = "+91 84088 20830";

const LOCATIONS = [
  {
    city: "Aurangabad",
    address: "Rauza Baugh, Aurangabad (Chhatrapati Sambhajinagar), Maharashtra",
    map: "https://www.google.com/maps/search/?api=1&query=Rauza+Baugh+Aurangabad+Maharashtra",
  },
  {
    city: "Navi Mumbai",
    address: "Taloja Phase 1, Navi Mumbai, Maharashtra",
    map: "https://www.google.com/maps/search/?api=1&query=Taloja+Phase+1+Navi+Mumbai",
  },
];

const IMAGES = {
  hero: "./images/layer-cake-slicing-on-plate.jpg",
  slice: "./images/apricot-dessert-slice-with-cream.jpg",
  bowl: "./images/apricot-dessert-in-bowl.jpg",
  scoop: "./images/hand-scooping-apricot-dessert.jpg",
  tray: "./images/layered-apricot-dessert-in-bowl.jpg",
  qubaniClassic: "./images/qubani-classic.jpg",
  qubaniClassicTall: "./images/qubani-classic-portrait.jpg",
  brownieCheese: "./images/brownie-cream-cheese.jpg",
  brownie: "./images/brownie-walnut-fudge.jpg",
  halwa: "./images/badam-halwa.jpg",
  kaju: "./images/kaju-halwa-tin.jpg",
  gajar: "./images/gajar-halwa.jpg",
  gajarCups: "./images/gajar-halwa-cups.jpg",
  kajuPlate: "./images/kaju-halwa.jpg",
  gajarTray: "./images/gajar-halwa-tray.jpg",
};

const waLink = (message) =>
  `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;

const DEFAULT_WA_MESSAGE = `Hi ${BRAND}! I'd like to place an order.`;

/* ------------------------------------------------------------------ */
/*  Content                                                            */
/* ------------------------------------------------------------------ */

// Each page is a hash route: #home, #qubani, #brownies, #halwa, #bulk-orders
const ROUTES = [
  { id: "home", label: "Home" },
  { id: "qubani", label: "Qubani" },
  { id: "brownies", label: "Brownies" },
  { id: "halwa", label: "Halwa" },
  { id: "bulk-orders", label: "Bulk Orders" },
];

const LAYERS = [
  {
    id: "cream",
    name: "Fresh Whipped Cream & Almonds",
    short: "Crown",
    tagline: "The cloud on top",
    description:
      "Hand-whipped fresh dairy cream, barely sweetened and folded with a hint of cardamom, finished with toasted almond flakes and a scatter of pistachio.",
    notes: ["Fresh dairy cream", "Toasted almond flakes", "Green cardamom", "Pistachio dust"],
    texture: "Airy · Silky · Nutty crunch",
    swatch: "from-[#FFFDF8] to-[#F6EEDD]",
    dot: "bg-[#F3E7CF]",
  },
  {
    id: "qubani",
    name: "Glossy Stewed Qubani Compote",
    short: "Heart",
    tagline: "The royal soul",
    description:
      "Sun-dried apricots soaked overnight, then slow-stewed for hours in the old Deccan way until jammy, glossy and deep amber. Every batch is hand-stirred and finished with a squeeze of lemon.",
    notes: ["Sun-dried apricots", "Slow-stewed 6 hours", "Saffron whisper", "No artificial colour"],
    texture: "Jammy · Glossy · Tangy-sweet",
    swatch: "from-[#F7A23B] to-[#D86A00]",
    dot: "bg-[#F28500]",
  },
  {
    id: "sponge",
    name: "Moist Premium Sponge",
    short: "Base",
    tagline: "The tender foundation",
    description:
      "A golden butter sponge baked fresh each morning and soaked in a light apricot-milk syrup, so every forkful is moist from edge to centre without ever turning soggy.",
    notes: ["Cultured butter", "Free-range eggs", "Apricot-milk soak", "Baked daily"],
    texture: "Soft · Buttery · Melt-in-mouth",
    swatch: "from-[#F1D18A] to-[#D9AE57]",
    dot: "bg-[#E2B865]",
  },
];

const REVIEWS = [
  {
    name: "Ayesha Siddiqui",
    place: "Aurangabad",
    text: "It tastes exactly like my Nani's Qubani ka Meetha — but somehow lighter. The cream and sponge make it feel like a celebration cake. Ordered three times this month.",
  },
  {
    name: "Rohan Deshpande",
    place: "Navi Mumbai",
    text: "Ordered the Apricot Delight gift box and a kilo of badam halwa for Diwali. The packaging got compliments, and both disappeared in ten minutes flat.",
  },
  {
    name: "Fatima Khan",
    place: "Hyderabad",
    text: "As a Hyderabadi I was sceptical. I'm not anymore. The classic Qubani is proper slow-stewed, not that jelly shortcut. And the walnut brownies are dangerously good.",
  },
];

const FAQS = [
  {
    q: "How do I place an order?",
    a: "Tap any 'Order on WhatsApp' button. A pre-filled message opens in WhatsApp; just confirm your size, date and address and we'll reply with payment details within minutes.",
  },
  {
    q: "How far in advance should I order?",
    a: "Half KG and 1 KG cakes can usually be delivered the same day if you order before 12 noon. For gift boxes and bulk orders (5+ cakes), please give us 24–48 hours.",
  },
  {
    q: "Are your desserts eggless?",
    a: "Qubani ka Meetha and both halwas are naturally eggless. The Apricot Delight sponge and brownies contain eggs, but eggless versions are available on request at no extra cost. Just mention it in your WhatsApp message.",
  },
  {
    q: "How should I store it, and how long does it last?",
    a: "Keep it refrigerated and enjoy within 48 hours. Take it out 10 minutes before serving so the cream softens and the Qubani becomes glossy again.",
  },
  {
    q: "Where do you deliver?",
    a: "We bake and deliver from two kitchens: Rauza Baugh in Aurangabad and Taloja Phase 1 in Navi Mumbai. Cakes go out in chilled boxes across both cities. For other cities, ask us about our Qubani compote jars, which travel well by courier.",
  },
  {
    q: "Do you take party, wedding and hotel orders?",
    a: "Yes. We cater birthdays, house parties, nikah and wedding functions, and supply hotels, restaurants and caterers regularly. Businesses get dedicated B2B rates (see the price list on our Bulk Orders page), and events get custom sizes, from dessert cups to full sheet cakes. Message us on WhatsApp with your date, guest count and venue.",
  },
];

const INGREDIENTS = [
  "Apricot Delight",
  "Qubani ka Meetha",
  "Walnut fudge brownies",
  "Badam halwa",
  "Kaju halwa",
  "Gajar ka halwa",
  "Sun-dried apricots",
  "Fresh dairy cream",
  "Toasted almonds",
  "Kashmiri saffron",
  "Green cardamom",
  "Cultured butter",
  "Pistachio dust",
  "Baked fresh daily",
];

/* ------------------------------------------------------------------ */
/*  Hooks & small building blocks                                      */
/* ------------------------------------------------------------------ */

function useReveal() {
  const ref = useRef(null);
  const [visible, setVisible] = useState(false);
  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (typeof IntersectionObserver === "undefined") {
      setVisible(true);
      return;
    }
    const io = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setVisible(true);
          io.disconnect();
        }
      },
      { threshold: 0.15 }
    );
    io.observe(el);
    return () => io.disconnect();
  }, []);
  return [ref, visible];
}

function Reveal({ children, delay = 0, className = "" }) {
  const [ref, visible] = useReveal();
  return (
    <div
      ref={ref}
      style={{ transitionDelay: `${delay}ms` }}
      className={`transition-all duration-700 ease-out motion-reduce:transition-none ${
        visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-8"
      } ${className}`}
    >
      {children}
    </div>
  );
}

function Eyebrow({ children, light = false }) {
  return (
    <span
      className={`inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.28em] ${
        light ? "text-[#F7C27A]" : "text-[#C46A00]"
      }`}
    >
      <span className={`h-px w-8 ${light ? "bg-[#F7C27A]" : "bg-[#C46A00]"}`} />
      {children}
    </span>
  );
}

function SectionHeading({ eyebrow, title, subtitle, light = false, center = true }) {
  return (
    <div className={`max-w-2xl ${center ? "mx-auto text-center" : ""}`}>
      <Eyebrow light={light}>{eyebrow}</Eyebrow>
      <h2
        className={`font-display mt-4 text-4xl leading-[1.05] sm:text-5xl ${
          light ? "text-[#FDFBF7]" : "text-[#1F1B16]"
        }`}
      >
        {title}
      </h2>
      {subtitle && (
        <p className={`mt-5 text-base leading-relaxed sm:text-lg ${light ? "text-[#E9E1D3]/80" : "text-[#5B5249]"}`}>
          {subtitle}
        </p>
      )}
    </div>
  );
}

function WhatsAppButton({ message = DEFAULT_WA_MESSAGE, children, variant = "primary", className = "" }) {
  const styles = {
    primary:
      "bg-[#F28500] text-white shadow-[0_12px_30px_-10px_rgba(242,133,0,0.7)] hover:bg-[#D97700] hover:shadow-[0_18px_40px_-12px_rgba(242,133,0,0.8)]",
    dark: "bg-[#1F1B16] text-[#FDFBF7] hover:bg-[#3A322A] shadow-[0_12px_30px_-12px_rgba(31,27,22,0.6)]",
    ghost: "border border-[#1F1B16]/15 bg-white/60 text-[#1F1B16] backdrop-blur hover:border-[#1F1B16]/40 hover:bg-white",
  };
  return (
    <a
      href={waLink(message)}
      target="_blank"
      rel="noopener noreferrer"
      className={`group inline-flex items-center justify-center gap-2 rounded-full px-6 py-3.5 text-sm font-semibold tracking-wide transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F28500] focus-visible:ring-offset-2 ${styles[variant]} ${className}`}
    >
      <MessageCircle className="h-4 w-4 transition-transform duration-300 group-hover:rotate-[-12deg]" />
      {children}
    </a>
  );
}

const formatINR = (n) => `₹${n.toLocaleString("en-IN")}`;

const CATEGORIES = [
  {
    id: "qubani",
    name: "Qubani",
    eyebrow: "Category 01 · The royal apricot",
    title: (
      <>
        Qubani, <em className="text-[#F28500]">two ways.</em>
      </>
    ),
    intro:
      "Our house speciality. Sun-dried apricots slow-stewed the old Deccan way, served as the timeless classic, or as Apricot Delight, our premium layered cake with fresh cream.",
    cardText: "Classic Qubani ka Meetha and our premium Apricot Delight layer cake.",
    image: IMAGES.qubaniClassic,
    products: [
      {
        id: "qubani-classic",
        name: "Qubani ka Meetha",
        badge: "The classic",
        description:
          "The heirloom recipe: whole apricots slow-stewed into a glossy, jammy compote, finished with a dollop of fresh cream, almond flakes and pistachio.",
        image: IMAGES.qubaniClassic,
        options: [
          { label: "250 g", price: 349, b2b: 290, note: "Serves 2" },
          { label: "500 g", price: 629, b2b: 530, note: "Serves 4" },
          { label: "1 KG", price: 1179, b2b: 1000, note: "Serves 8" },
        ],
        features: ["Slow-stewed 6 hours", "No artificial colour", "Cream, almond & pistachio topping"],
      },
      {
        id: "apricot-delight",
        name: "Apricot Delight",
        badge: "Premium · Bestseller",
        premium: true,
        description:
          "Qubani ka Meetha reimagined as a celebration cake: moist butter sponge, a thick layer of Qubani compote and a cloud of fresh whipped cream with toasted almonds.",
        image: IMAGES.hero,
        options: [
          { label: "Half KG", price: 1039, b2b: 880, note: "Serves 4–5" },
          { label: "1 KG", price: 1949, b2b: 1650, note: "Serves 8–10" },
          { label: "Gift Box", price: 2649, b2b: 2240, note: "1 KG + compote jar" },
        ],
        features: ["Three layers: cream, Qubani, sponge", "Hand-piped fresh cream", "Gift box with greeting card"],
      },
    ],
    highlights: [
      [Leaf, "Sun-dried apricots", "Soaked overnight, never canned"],
      [Clock, "Slow-stewed 6 hours", "Hand-stirred in small batches"],
      [ShieldCheck, "No artificial colour", "Amber comes from the fruit"],
    ],
  },
  {
    id: "brownies",
    name: "Chocolate Brownies",
    eyebrow: "Category 02 · Rich & fudgy",
    title: (
      <>
        Fudgy, <em className="text-[#F28500]">deeply chocolate.</em>
      </>
    ),
    intro:
      "Dense, crackle-topped brownies baked with real dark chocolate and cultured butter. Gooey in the centre, chewy at the edges, and made to share (or not).",
    cardText: "Walnut fudge and cream cheese swirl brownies, baked daily.",
    image: IMAGES.brownie,
    products: [
      {
        id: "brownie-walnut",
        name: "Walnut Fudge Brownie",
        badge: "Bestseller",
        premium: true,
        description:
          "Our signature fudgy brownie, drizzled with warm chocolate ganache and topped with toasted walnuts and a pinch of sea salt.",
        image: IMAGES.brownie,
        options: [
          { label: "Box of 4", price: 349, note: "4 large squares" },
          { label: "Box of 9", price: 749, note: "9 large squares" },
          { label: "Box of 16", price: 1249, note: "Party box" },
        ],
        features: ["Dark chocolate ganache drizzle", "Toasted walnuts & sea salt", "Gooey centre, chewy edges"],
      },
      {
        id: "brownie-cheesecake",
        name: "Cream Cheese Swirl Brownie",
        badge: "New",
        description:
          "A fudgy chocolate base marbled with a silky vanilla cream-cheese swirl. Rich, tangy and beautifully balanced.",
        image: IMAGES.brownieCheese,
        options: [
          { label: "Box of 4", price: 399, note: "4 large squares" },
          { label: "Box of 9", price: 849, note: "9 large squares" },
        ],
        features: ["Vanilla cream-cheese marble", "Real dark chocolate", "Packed in a kraft gift box"],
      },
    ],
    highlights: [
      [Award, "Real dark chocolate", "No compound, no shortcuts"],
      [Clock, "Baked fresh daily", "Cut and boxed to order"],
      [Gift, "Ready to gift", "Kraft boxes with ribbon"],
    ],
  },
  {
    id: "halwa",
    name: "Halwa",
    eyebrow: "Category 03 · Pure ghee classics",
    title: (
      <>
        Slow-roasted <em className="text-[#F28500]">in pure ghee.</em>
      </>
    ),
    intro:
      "Rich, festive halwas the way Deccan homes make them: badam and kaju roasted low and slow in pure ghee, plus our winter-special gajar ka halwa made with fresh red carrots.",
    cardText: "Badam, kaju and winter-special gajar ka halwa, made in pure ghee.",
    image: IMAGES.halwa,
    products: [
      {
        id: "badam-halwa",
        name: "Badam Halwa",
        badge: "Royal favourite",
        premium: true,
        description:
          "Freshly ground almonds roasted in pure desi ghee until golden and fragrant, with Kashmiri saffron, green cardamom and silver vark.",
        image: IMAGES.halwa,
        options: [
          { label: "250 g", price: 629, b2b: 530, note: "Serves 3–4" },
          { label: "500 g", price: 1179, b2b: 1000, note: "Serves 6–8" },
          { label: "1 KG", price: 2239, b2b: 1890, note: "Serves 12–15" },
        ],
        features: ["Pure desi ghee", "Kashmiri saffron & cardamom", "Finished with silver vark"],
      },
      {
        id: "kaju-halwa",
        name: "Kaju Halwa",
        badge: "Gift-tin favourite",
        description:
          "Rich cashew halwa slow-cooked in ghee until deep golden-brown, set and cut into squares, then topped with whole cashews and pistachio.",
        image: IMAGES.kaju,
        options: [
          { label: "250 g", price: 559, b2b: 470, note: "Serves 3–4" },
          { label: "500 g", price: 1039, b2b: 880, note: "Serves 6–8" },
          { label: "1 KG", price: 1949, b2b: 1650, note: "Serves 12–15" },
        ],
        features: ["Premium whole cashews", "Cut into squares, easy to serve", "Festive gift tins available"],
      },
      {
        id: "gajar-halwa",
        name: "Gajar ka Halwa",
        badge: "Winter special",
        seasonal: true,
        description:
          "The winter classic: fresh red carrots grated by hand and slow-cooked with milk, ghee and cardamom, loaded with cashews, almonds, pistachio and raisins.",
        image: IMAGES.gajar,
        options: [
          { label: "250 g", price: 279, b2b: 230, note: "Serves 2–3" },
          { label: "500 g", price: 539, b2b: 450, note: "Serves 5–6" },
          { label: "1 KG", price: 969, b2b: 820, note: "Serves 10–12" },
        ],
        features: ["Fresh winter red carrots", "Slow-cooked with milk & ghee", "Loaded with dry fruits"],
      },
    ],
    highlights: [
      [Flame, "Slow-roasted", "Low flame, hand-stirred"],
      [Droplets, "Pure desi ghee", "Never vanaspati or oil"],
      [Gift, "Festive tins", "Diwali, Eid & weddings"],
    ],
  },
];

const findProduct = (id) => CATEGORIES.flatMap((c) => c.products).find((p) => p.id === id);
const fromPrice = (category) => Math.min(...category.products.flatMap((p) => p.options.map((o) => o.price)));


/* ------------------------------------------------------------------ */
/*  Sections                                                           */
/* ------------------------------------------------------------------ */

function useHashRoute() {
  const read = () => {
    const id = (typeof window !== "undefined" ? window.location.hash : "").replace("#", "");
    return ROUTES.some((r) => r.id === id) ? id : "home";
  };
  const [route, setRoute] = useState(read);
  useEffect(() => {
    const onHash = () => {
      setRoute(read());
      window.scrollTo({ top: 0, behavior: "auto" });
    };
    window.addEventListener("hashchange", onHash);
    return () => window.removeEventListener("hashchange", onHash);
  }, []);
  return route;
}

function Navbar({ route }) {
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => setOpen(false), [route]);

  return (
    <header
      className={`fixed inset-x-0 top-0 z-50 transition-all duration-500 ${
        scrolled || open || route !== "home"
          ? "bg-[#FDFBF7]/85 shadow-[0_1px_0_rgba(31,27,22,0.06)] backdrop-blur-xl"
          : "bg-transparent"
      }`}
    >
      <nav className="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8">
        <a href="#home" className="group flex items-center gap-2.5" aria-label={`${BRAND} home`}>
          <span className="grid h-10 w-10 place-items-center rounded-full bg-[#1F1B16] text-[#E9B949] transition-transform duration-500 group-hover:rotate-12">
            <Crown className="h-5 w-5" />
          </span>
          <span className="leading-none">
            <span className="font-display block text-2xl text-[#1F1B16]">{BRAND}</span>
            <span className="mt-1 block text-[9px] font-semibold uppercase tracking-[0.32em] text-[#C46A00]">
              Deccan Fusion Bakery
            </span>
          </span>
        </a>

        <ul className="hidden items-center gap-1 rounded-full border border-[#1F1B16]/5 bg-white/50 p-1 backdrop-blur lg:flex">
          {ROUTES.map((r) => (
            <li key={r.id}>
              <a
                href={`#${r.id}`}
                aria-current={route === r.id ? "page" : undefined}
                className={`block rounded-full px-4 py-2 text-sm font-medium transition-all duration-300 ${
                  route === r.id
                    ? "bg-[#1F1B16] text-[#FDFBF7] shadow"
                    : "text-[#3A322A] hover:bg-white hover:text-[#1F1B16]"
                }`}
              >
                {r.label}
              </a>
            </li>
          ))}
        </ul>

        <div className="flex items-center gap-3">
          <WhatsAppButton className="hidden !px-5 !py-2.5 sm:inline-flex">Order on WhatsApp</WhatsAppButton>
          <button
            onClick={() => setOpen((o) => !o)}
            className="grid h-11 w-11 place-items-center rounded-full border border-[#1F1B16]/10 bg-white/70 text-[#1F1B16] lg:hidden"
            aria-label={open ? "Close menu" : "Open menu"}
            aria-expanded={open}
          >
            {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
          </button>
        </div>
      </nav>

      <div
        className={`overflow-hidden transition-[max-height] duration-500 lg:hidden ${open ? "max-h-[30rem]" : "max-h-0"}`}
      >
        <ul className="space-y-1 px-5 pb-6">
          {ROUTES.map((r) => (
            <li key={r.id}>
              <a
                href={`#${r.id}`}
                onClick={() => setOpen(false)}
                className={`flex items-center justify-between rounded-2xl px-4 py-3.5 text-base font-medium ${
                  route === r.id ? "bg-[#1F1B16] text-[#FDFBF7]" : "text-[#1F1B16] hover:bg-[#F6EEDD]"
                }`}
              >
                {r.label}
                <ArrowRight className={`h-4 w-4 ${route === r.id ? "text-[#F7A23B]" : "text-[#C46A00]"}`} />
              </a>
            </li>
          ))}
          <li className="pt-3">
            <WhatsAppButton className="w-full">Order on WhatsApp</WhatsAppButton>
          </li>
        </ul>
      </div>
    </header>
  );
}

function Hero() {
  return (
    <section id="top" className="relative overflow-hidden pt-28 sm:pt-32 lg:pt-36">
      {/* Background glows */}
      <div className="pointer-events-none absolute -right-40 -top-40 h-[36rem] w-[36rem] rounded-full bg-[#F28500]/15 blur-3xl" />
      <div className="pointer-events-none absolute -left-32 top-1/2 h-80 w-80 rounded-full bg-[#E9B949]/20 blur-3xl" />

      <div className="relative mx-auto grid max-w-7xl items-center gap-14 px-5 pb-20 sm:px-8 lg:grid-cols-12 lg:gap-10 lg:pb-28">
        <div className="lg:col-span-6">
          <Reveal>
            <span className="inline-flex items-center gap-2 rounded-full border border-[#F28500]/25 bg-white/70 px-4 py-2 text-xs font-medium text-[#8A4B00] backdrop-blur">
              <Sparkles className="h-3.5 w-3.5 text-[#F28500]" />
              Qubani · Brownies · Halwa · Made fresh daily
            </span>
          </Reveal>

          <Reveal delay={100}>
            <h1 className="font-display mt-7 text-[3.2rem] leading-[0.98] text-[#1F1B16] sm:text-7xl xl:text-[5.4rem]">
              The Royal <em className="text-[#F28500]">Qubani</em>,
              <br />
              Reimagined.
            </h1>
          </Reveal>

          <Reveal delay={200}>
            <p className="mt-7 max-w-xl text-lg leading-relaxed text-[#5B5249]">
              Home of <strong className="font-semibold text-[#1F1B16]">Apricot Delight</strong>: slow-stewed Qubani
              layered with butter-soft sponge and fresh cream. Plus the classic Qubani ka Meetha, fudgy chocolate
              brownies and pure-ghee badam &amp; kaju halwa.
            </p>
          </Reveal>

          <Reveal delay={300}>
            <div className="mt-10 flex flex-col gap-3 sm:flex-row">
              <WhatsAppButton>Order on WhatsApp</WhatsAppButton>
              <a
                href="#qubani"
                className="group inline-flex items-center justify-center gap-2 rounded-full border border-[#1F1B16]/15 bg-white/60 px-6 py-3.5 text-sm font-semibold text-[#1F1B16] backdrop-blur transition-all duration-300 hover:-translate-y-0.5 hover:border-[#1F1B16]/40 hover:bg-white"
              >
                Explore Qubani
                <ArrowRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
              </a>
            </div>
          </Reveal>

          <Reveal delay={400}>
            <dl className="mt-12 grid max-w-lg grid-cols-3 divide-x divide-[#1F1B16]/10 border-t border-[#1F1B16]/10 pt-8">
              {[
                ["4.9", "Avg. rating", <Star key="s" className="h-4 w-4 fill-[#E9B949] text-[#E9B949]" />],
                ["6 hr", "Slow-stewed", <Clock key="c" className="h-4 w-4 text-[#F28500]" />],
                ["2k+", "Cakes gifted", <Heart key="h" className="h-4 w-4 fill-[#F28500] text-[#F28500]" />],
              ].map(([value, label, icon], i) => (
                <div key={label} className={i === 0 ? "pr-4" : "px-4"}>
                  <dt className="flex items-center gap-1.5 text-xs uppercase tracking-wider text-[#8C8177]">
                    {icon}
                    {label}
                  </dt>
                  <dd className="font-display mt-1 text-3xl text-[#1F1B16]">{value}</dd>
                </div>
              ))}
            </dl>
          </Reveal>
        </div>

        {/* Visual */}
        <div className="relative lg:col-span-6">
          <Reveal delay={150}>
            <div className="relative mx-auto max-w-xl">
              <div className="absolute -inset-4 rounded-[3rem] border border-[#E9B949]/40 sm:-inset-6" />
              <div className="relative overflow-hidden rounded-[2.5rem] shadow-[0_40px_80px_-30px_rgba(120,60,0,0.45)]">
                <img
                  src={IMAGES.hero}
                  alt="A slice of Apricot Delight layer cake with fresh cream, apricot compote and almonds on a marble plate"
                  className="aspect-[4/5] w-full object-cover object-[55%_50%] transition-transform duration-[1.5s] ease-out hover:scale-105 sm:aspect-[5/6]"
                />
                <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#1F1B16]/40 via-transparent to-transparent" />
              </div>

              {/* Floating cards */}
              <div className="animate-float absolute -left-3 bottom-10 flex items-center gap-3 rounded-2xl bg-white/90 p-3 pr-5 shadow-xl backdrop-blur sm:-left-10">
                <span className="grid h-11 w-11 place-items-center rounded-xl bg-[#FFF1DE] text-[#F28500]">
                  <Leaf className="h-5 w-5" />
                </span>
                <div>
                  <p className="text-sm font-semibold text-[#1F1B16]">Sun-dried apricots</p>
                  <p className="text-xs text-[#8C8177]">No artificial colour</p>
                </div>
              </div>

              <div className="animate-float-slow absolute -right-2 top-10 rounded-2xl bg-[#1F1B16]/90 px-5 py-4 text-[#FDFBF7] shadow-xl backdrop-blur sm:-right-8">
                <p className="text-[10px] uppercase tracking-[0.25em] text-[#E9B949]">Apricot Delight</p>
                <p className="font-display text-3xl leading-none">{formatINR(findProduct("apricot-delight").options[0].price)}</p>
                <p className="mt-1 text-xs text-[#E9E1D3]/70">Half KG · Serves 4–5</p>
              </div>
            </div>
          </Reveal>
        </div>
      </div>

      {/* Ingredient marquee */}
      <div className="relative border-y border-[#1F1B16]/10 bg-[#1F1B16] py-4 text-[#FDFBF7]">
        <div className="flex overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_10%,#000_90%,transparent)]">
          <div className="animate-marquee flex shrink-0 items-center gap-10 pr-10">
            {[...INGREDIENTS, ...INGREDIENTS].map((item, i) => (
              <span key={i} className="flex items-center gap-10 whitespace-nowrap">
                <span className="font-display text-xl italic">{item}</span>
                <Sparkles className="h-4 w-4 text-[#F28500]" />
              </span>
            ))}
          </div>
          <div aria-hidden className="animate-marquee flex shrink-0 items-center gap-10 pr-10">
            {[...INGREDIENTS, ...INGREDIENTS].map((item, i) => (
              <span key={i} className="flex items-center gap-10 whitespace-nowrap">
                <span className="font-display text-xl italic">{item}</span>
                <Sparkles className="h-4 w-4 text-[#F28500]" />
              </span>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function CakeLayer({ layer, index, active, onSelect }) {
  const isActive = active === layer.id;
  const heights = ["h-20 sm:h-24", "h-14 sm:h-16", "h-24 sm:h-28"];
  return (
    <button
      type="button"
      onMouseEnter={() => onSelect(layer.id)}
      onFocus={() => onSelect(layer.id)}
      onClick={() => onSelect(layer.id)}
      aria-pressed={isActive}
      aria-label={layer.name}
      className={`group relative block w-full text-left transition-all duration-500 ease-out focus:outline-none ${
        isActive ? "z-10 scale-[1.03] drop-shadow-[0_18px_25px_rgba(120,60,0,0.25)]" : "scale-100 opacity-75 hover:opacity-100"
      }`}
    >
      <div
        className={`relative ${heights[index]} overflow-hidden bg-gradient-to-b ${layer.swatch} ${
          index === 0 ? "rounded-t-[2.5rem]" : ""
        } ${index === 2 ? "rounded-b-2xl" : ""} ${
          isActive ? "ring-2 ring-[#1F1B16]/80 ring-offset-2 ring-offset-[#FDFBF7]" : ""
        } shadow-[inset_0_-8px_16px_rgba(0,0,0,0.06)] transition-shadow`}
      >
        {/* textures */}
        {layer.id === "cream" && (
          <>
            {[12, 26, 41, 55, 68, 83].map((left, i) => (
              <span
                key={i}
                style={{ left: `${left}%`, top: `${i % 2 ? 22 : 34}%`, transform: `rotate(${i * 37}deg)` }}
                className="absolute h-2.5 w-6 rounded-full bg-gradient-to-r from-[#E8C99A] to-[#C99A5E] shadow-sm"
              />
            ))}
            {[20, 48, 76].map((left, i) => (
              <span
                key={`p${i}`}
                style={{ left: `${left}%`, top: "58%" }}
                className="absolute h-1.5 w-1.5 rounded-full bg-[#9DBA5A]"
              />
            ))}
          </>
        )}
        {layer.id === "qubani" && (
          <>
            <div className="absolute inset-x-0 top-0 h-1/3 bg-gradient-to-b from-white/35 to-transparent" />
            {[8, 24, 40, 58, 74, 88].map((left, i) => (
              <span
                key={i}
                style={{ left: `${left}%`, top: `${i % 2 ? 30 : 45}%` }}
                className="absolute h-4 w-5 rounded-full bg-[#B35500]/50 blur-[1px]"
              />
            ))}
          </>
        )}
        {layer.id === "sponge" && (
          <div className="absolute inset-0 opacity-40 [background-image:radial-gradient(#B98A3A_1px,transparent_1.5px)] [background-size:10px_10px]" />
        )}

        <span
          className={`absolute right-4 top-1/2 -translate-y-1/2 rounded-full px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] transition-all duration-300 ${
            isActive
              ? "bg-[#1F1B16] text-[#FDFBF7]"
              : "bg-white/70 text-[#1F1B16] opacity-0 group-hover:opacity-100"
          }`}
        >
          {layer.short}
        </span>
      </div>
    </button>
  );
}

function LayerBreakdown() {
  const [active, setActive] = useState("qubani");
  const current = LAYERS.find((l) => l.id === active);

  return (
    <section id="layers" className="relative scroll-mt-20 py-24 sm:py-32">
      <div className="mx-auto max-w-7xl px-5 sm:px-8">
        <Reveal>
          <SectionHeading
            eyebrow="Anatomy of Apricot Delight"
            title={
              <>
                Three layers. <em className="text-[#F28500]">One royal bite.</em>
              </>
            }
            subtitle="Hover over or tap each layer of the cake to discover what goes into it, and why it matters."
          />
        </Reveal>

        <div className="mt-16 grid items-center gap-14 lg:grid-cols-2 lg:gap-20">
          {/* Interactive cake */}
          <Reveal>
            <div className="relative mx-auto w-full max-w-md">
              <div className="absolute -inset-10 rounded-full bg-[#F28500]/10 blur-3xl" />
              <div className="relative">
                {/* Tab pills for mobile clarity */}
                <div className="mb-8 flex flex-wrap justify-center gap-2" role="tablist" aria-label="Cake layers">
                  {LAYERS.map((l) => (
                    <button
                      key={l.id}
                      role="tab"
                      aria-selected={active === l.id}
                      onClick={() => setActive(l.id)}
                      className={`flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold transition-all duration-300 ${
                        active === l.id
                          ? "bg-[#1F1B16] text-[#FDFBF7] shadow-lg"
                          : "bg-white text-[#5B5249] ring-1 ring-[#1F1B16]/10 hover:ring-[#1F1B16]/30"
                      }`}
                    >
                      <span className={`h-2 w-2 rounded-full ${l.dot}`} />
                      {l.short}
                    </button>
                  ))}
                </div>

                <div className="space-y-1.5 px-3 sm:px-0">
                  {LAYERS.map((layer, i) => (
                    <CakeLayer key={layer.id} layer={layer} index={i} active={active} onSelect={setActive} />
                  ))}
                </div>
                {/* Plate */}
                <div className="mx-auto mt-3 h-4 w-[110%] -translate-x-[4.5%] rounded-full bg-gradient-to-b from-white to-[#E7E0D4] shadow-[0_20px_40px_-15px_rgba(31,27,22,0.35)]" />
              </div>
            </div>
          </Reveal>

          {/* Description panel */}
          <Reveal delay={150}>
            <div className="relative overflow-hidden rounded-[2rem] bg-white p-8 shadow-[0_30px_70px_-35px_rgba(31,27,22,0.35)] ring-1 ring-[#1F1B16]/5 sm:p-10">
              <div key={current.id} className="animate-fade-up">
                <p className="text-xs font-semibold uppercase tracking-[0.28em] text-[#C46A00]">{current.tagline}</p>
                <h3 className="font-display mt-3 text-3xl leading-tight text-[#1F1B16] sm:text-4xl">{current.name}</h3>
                <p className="mt-5 leading-relaxed text-[#5B5249]">{current.description}</p>

                <div className="mt-7 grid grid-cols-2 gap-3">
                  {current.notes.map((n) => (
                    <div key={n} className="flex items-center gap-2.5 rounded-xl bg-[#FDFBF7] px-3.5 py-3 text-sm text-[#3A322A]">
                      <Check className="h-4 w-4 shrink-0 text-[#F28500]" />
                      {n}
                    </div>
                  ))}
                </div>

                <div className="mt-7 flex items-center justify-between gap-4 border-t border-[#1F1B16]/10 pt-6">
                  <div>
                    <p className="text-[10px] uppercase tracking-[0.25em] text-[#8C8177]">Texture</p>
                    <p className="mt-1 font-medium text-[#1F1B16]">{current.texture}</p>
                  </div>
                  <img
                    src={IMAGES.slice}
                    alt="Close-up of the cake's layers"
                    className="h-16 w-16 rounded-2xl object-cover ring-4 ring-[#FFF1DE]"
                  />
                </div>
              </div>
            </div>
          </Reveal>
        </div>
      </div>
    </section>
  );
}

function Story() {
  const pillars = [
    {
      icon: Crown,
      title: "A Nizami heirloom",
      text: "Qubani ka Meetha was the crown of Deccan wedding feasts for centuries, served in tiny bowls after a royal dawat.",
    },
    {
      icon: Award,
      title: "Patisserie technique",
      text: "We pair that recipe with modern French layering, precise temperatures and fresh-cream stabilising — never shortcuts.",
    },
    {
      icon: Heart,
      title: "Small batches, by hand",
      text: "Every compote is stirred by hand and every cake is assembled to order, so it reaches you at its freshest.",
    },
  ];

  return (
    <section id="story" className="relative scroll-mt-20 overflow-hidden bg-[#1F1B16] py-24 text-[#FDFBF7] sm:py-32">
      <div className="pointer-events-none absolute inset-0 opacity-[0.07] [background-image:radial-gradient(#E9B949_1px,transparent_1px)] [background-size:22px_22px]" />
      <div className="pointer-events-none absolute -right-40 top-20 h-96 w-96 rounded-full bg-[#F28500]/20 blur-3xl" />

      <div className="relative mx-auto grid max-w-7xl items-center gap-16 px-5 sm:px-8 lg:grid-cols-2 lg:gap-24">
        {/* Collage */}
        <Reveal>
          <div className="relative mx-auto grid max-w-lg grid-cols-5 gap-4">
            <div className="col-span-3 overflow-hidden rounded-[2rem]">
              <img
                src={IMAGES.scoop}
                alt="A spoonful of Qubani cake lifted from a terracotta bowl"
                className="aspect-[3/4] h-full w-full object-cover transition-transform duration-[1.2s] hover:scale-105"
              />
            </div>
            <div className="col-span-2 flex flex-col gap-4 pt-12">
              <div className="overflow-hidden rounded-[2rem]">
                <img
                  src={IMAGES.bowl}
                  alt="Qubani dessert in a clay bowl topped with almonds and pistachio"
                  className="aspect-square w-full object-cover transition-transform duration-[1.2s] hover:scale-105"
                />
              </div>
              <div className="rounded-[2rem] border border-[#E9B949]/30 p-5">
                <p className="font-display text-5xl text-[#E9B949]">1724</p>
                <p className="mt-1 text-xs leading-relaxed text-[#E9E1D3]/70">
                  The Asaf Jahi era begins in the Deccan, and with it, a legendary dessert table.
                </p>
              </div>
            </div>
          </div>
        </Reveal>

        <div>
          <Reveal>
            <SectionHeading
              light
              center={false}
              eyebrow="Our story"
              title={
                <>
                  Where the Deccan's <em className="text-[#F7A23B]">royal kitchens</em> meet the modern oven.
                </>
              }
            />
          </Reveal>
          <Reveal delay={100}>
            <p className="mt-6 text-lg leading-relaxed text-[#E9E1D3]/80">
              Our founder grew up between Aurangabad and Hyderabad, where no wedding ended without a bowl of amber
              Qubani topped with cream. We wanted to honour that memory and make it something you could slice, share
              and gift. The result is a dessert that tastes like home and looks like a celebration.
            </p>
          </Reveal>

          <div className="mt-10 space-y-5">
            {pillars.map(({ icon: Icon, title, text }, i) => (
              <Reveal key={title} delay={150 + i * 100}>
                <div className="group flex gap-5 rounded-2xl border border-white/5 bg-white/[0.03] p-5 transition-all duration-300 hover:border-[#E9B949]/30 hover:bg-white/[0.06]">
                  <span className="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-[#F28500]/15 text-[#F7A23B] transition-colors duration-300 group-hover:bg-[#F28500] group-hover:text-white">
                    <Icon className="h-5 w-5" />
                  </span>
                  <div>
                    <h3 className="font-display text-2xl">{title}</h3>
                    <p className="mt-1 text-sm leading-relaxed text-[#E9E1D3]/70">{text}</p>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function Reviews() {
  return (
    <section id="reviews" className="relative scroll-mt-20 bg-[#F6EEDD]/60 py-24 sm:py-32">
      <div className="mx-auto max-w-7xl px-5 sm:px-8">
        <Reveal>
          <SectionHeading
            eyebrow="Kind words"
            title={
              <>
                Loved across the <em className="text-[#F28500]">Deccan.</em>
              </>
            }
          />
        </Reveal>

        <Reveal delay={100}>
          <div className="mt-8 flex items-center justify-center gap-3">
            <div className="flex">
              {Array.from({ length: 5 }).map((_, i) => (
                <Star key={i} className="h-5 w-5 fill-[#E9B949] text-[#E9B949]" />
              ))}
            </div>
            <span className="text-sm text-[#5B5249]">
              <strong className="text-[#1F1B16]">4.9/5</strong> from 600+ happy customers
            </span>
          </div>
        </Reveal>

        <div className="mt-14 grid gap-6 md:grid-cols-3">
          {REVIEWS.map((r, i) => (
            <Reveal key={r.name} delay={i * 120} className="h-full">
              <figure className="group relative flex h-full flex-col rounded-[2rem] bg-white p-8 shadow-[0_25px_60px_-40px_rgba(31,27,22,0.4)] ring-1 ring-[#1F1B16]/5 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_35px_70px_-35px_rgba(31,27,22,0.45)]">
                <Quote className="h-9 w-9 text-[#F28500]/25 transition-colors duration-300 group-hover:text-[#F28500]/60" />
                <div className="mt-4 flex">
                  {Array.from({ length: 5 }).map((_, j) => (
                    <Star key={j} className="h-4 w-4 fill-[#E9B949] text-[#E9B949]" />
                  ))}
                </div>
                <blockquote className="mt-4 flex-1 leading-relaxed text-[#3A322A]">“{r.text}”</blockquote>
                <figcaption className="mt-7 flex items-center gap-3 border-t border-[#1F1B16]/10 pt-6">
                  <span className="font-display grid h-11 w-11 place-items-center rounded-full bg-gradient-to-br from-[#F7A23B] to-[#D86A00] text-lg text-white">
                    {r.name.charAt(0)}
                  </span>
                  <div>
                    <p className="font-semibold text-[#1F1B16]">{r.name}</p>
                    <p className="flex items-center gap-1 text-xs text-[#8C8177]">
                      <MapPin className="h-3 w-3" /> {r.place}
                    </p>
                  </div>
                </figcaption>
              </figure>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

const EVENT_TYPES = [
  {
    icon: PartyPopper,
    title: "Parties & celebrations",
    text: "Birthdays, anniversaries, house parties and office get-togethers. Pre-cut slices, dessert cups or full cakes.",
    perks: ["Dessert cups & jars", "Custom message toppers", "From 10 guests"],
    message: "a party / celebration",
  },
  {
    icon: Heart,
    title: "Marriage & nikah",
    text: "Mehendi, haldi, nikah, walima and reception dessert counters. Qubani served the royal way at your wedding.",
    perks: ["Live dessert counters", "Wedding favour boxes", "Bulk pricing for 100+ guests"],
    message: "a marriage / wedding function",
    featured: true,
  },
  {
    icon: Hotel,
    title: "Hotels & restaurants",
    text: "Regular supply for hotels, restaurants, cafés and caterers. Consistent quality and reliable daily or weekly deliveries.",
    perks: ["Sheet cakes & portions", "Standing weekly orders", "Trade pricing"],
    message: "hotel / restaurant supply",
  },
];

function EventsSection({ showCards = true }) {
  return (
    <section id="events" className="scroll-mt-20 px-5 py-24 sm:px-8 sm:py-28">
      <div className="mx-auto max-w-7xl">
        <Reveal>
          <div className="relative overflow-hidden rounded-[2.5rem]">
            <div className="absolute inset-0 bg-gradient-to-br from-[#F7A23B] via-[#F28500] to-[#C45A00]" />
            <div className="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/15 blur-2xl" />
            <div className="relative grid items-center gap-10 p-8 sm:p-14 lg:grid-cols-5">
              <div className="lg:col-span-3">
                <p className="text-xs font-semibold uppercase tracking-[0.3em] text-white/80">
                  Party · Marriage · Hotel orders
                </p>
                <h2 className="font-display mt-4 text-4xl leading-[1.05] text-white sm:text-6xl">
                  Bring the royal Qubani to your big day.
                </h2>
                <p className="mt-5 max-w-lg text-lg text-white/85">
                  From an intimate birthday to a grand walima, plus regular supply for hotels and caterers. Bulk
                  pricing, custom sizes and on-time delivery across Aurangabad and Navi Mumbai.
                </p>
                <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                  <WhatsAppButton
                    variant="dark"
                    message={`Hi ${BRAND}! I'd like a quote for a bulk order. Event type: ___, Date: ___, Guests: ___, Venue: ___`}
                  >
                    Get a bulk quote
                  </WhatsAppButton>
                  <a
                    href={`tel:+${WHATSAPP_NUMBER}`}
                    className="inline-flex items-center justify-center gap-2 rounded-full border border-white/40 px-6 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 hover:bg-white/10"
                  >
                    <Phone className="h-4 w-4" /> Call {PHONE_DISPLAY}
                  </a>
                </div>
              </div>
              <div className="relative hidden lg:col-span-2 lg:block">
                <img
                  src={IMAGES.tray}
                  alt="A family-size tray of layered Qubani dessert topped with almonds"
                  className="ml-auto aspect-[3/4] w-72 rotate-3 rounded-[2rem] object-cover shadow-2xl ring-8 ring-white/20 transition-transform duration-700 hover:rotate-0"
                />
                <div className="absolute bottom-8 left-0 flex items-center gap-3 rounded-2xl bg-white p-3 pr-5 shadow-xl">
                  <span className="grid h-11 w-11 place-items-center rounded-xl bg-[#FFF1DE] text-[#F28500]">
                    <Users className="h-5 w-5" />
                  </span>
                  <div>
                    <p className="text-sm font-semibold text-[#1F1B16]">Small parties to big weddings</p>
                    <p className="text-xs text-[#8C8177]">Special bulk pricing</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </Reveal>

        {!showCards && (
          <div className="mt-6 flex justify-center">
            <a
              href="#bulk-orders"
              className="group inline-flex items-center gap-2 text-sm font-semibold text-[#C46A00] transition-colors hover:text-[#1F1B16]"
            >
              See party, marriage & hotel packages
              <ArrowRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
            </a>
          </div>
        )}
        {showCards && (
        <div className="mt-8 grid gap-6 md:grid-cols-3">
          {EVENT_TYPES.map(({ icon: Icon, title, text, perks, message, featured }, i) => (
            <Reveal key={title} delay={i * 120} className="h-full">
              <article
                className={`group flex h-full flex-col rounded-[2rem] p-8 transition-all duration-500 hover:-translate-y-1.5 ${
                  featured
                    ? "bg-[#1F1B16] text-[#FDFBF7] shadow-[0_35px_70px_-35px_rgba(31,27,22,0.7)]"
                    : "bg-white text-[#1F1B16] shadow-[0_25px_60px_-40px_rgba(31,27,22,0.4)] ring-1 ring-[#1F1B16]/5 hover:shadow-[0_35px_70px_-35px_rgba(31,27,22,0.45)]"
                }`}
              >
                <span
                  className={`grid h-14 w-14 place-items-center rounded-2xl transition-all duration-300 group-hover:rotate-[-6deg] ${
                    featured ? "bg-[#F28500] text-white" : "bg-[#FFF1DE] text-[#F28500]"
                  }`}
                >
                  <Icon className="h-6 w-6" />
                </span>
                <h3 className="font-display mt-6 text-3xl">{title}</h3>
                <p className={`mt-3 leading-relaxed ${featured ? "text-[#E9E1D3]/75" : "text-[#5B5249]"}`}>{text}</p>
                <ul className="mt-6 flex-1 space-y-2.5">
                  {perks.map((perk) => (
                    <li key={perk} className="flex items-center gap-2.5 text-sm">
                      <Check className={`h-4 w-4 shrink-0 ${featured ? "text-[#F7A23B]" : "text-[#F28500]"}`} />
                      {perk}
                    </li>
                  ))}
                </ul>
                <a
                  href={waLink(`Hi ${BRAND}! I'd like a quote for ${message}. Date: ___, Guests: ___, Venue: ___`)}
                  target="_blank"
                  rel="noopener noreferrer"
                  className={`mt-8 inline-flex items-center gap-2 text-sm font-semibold transition-colors ${
                    featured ? "text-[#F7A23B] hover:text-white" : "text-[#C46A00] hover:text-[#1F1B16]"
                  }`}
                >
                  Enquire on WhatsApp
                  <ArrowRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
                </a>
              </article>
            </Reveal>
          ))}
        </div>
        )}
      </div>
    </section>
  );
}

function FAQItem({ item, open, onToggle, index }) {
  return (
    <div
      className={`rounded-2xl border transition-all duration-300 ${
        open ? "border-[#F28500]/30 bg-white shadow-[0_20px_50px_-35px_rgba(31,27,22,0.4)]" : "border-[#1F1B16]/10 bg-white/50 hover:bg-white"
      }`}
    >
      <button
        type="button"
        onClick={onToggle}
        aria-expanded={open}
        aria-controls={`faq-${index}`}
        className="flex w-full items-center justify-between gap-6 px-6 py-5 text-left"
      >
        <span className="font-medium text-[#1F1B16] sm:text-lg">{item.q}</span>
        <span
          className={`grid h-9 w-9 shrink-0 place-items-center rounded-full transition-all duration-300 ${
            open ? "rotate-180 bg-[#F28500] text-white" : "bg-[#FDFBF7] text-[#1F1B16]"
          }`}
        >
          <ChevronDown className="h-4 w-4" />
        </span>
      </button>
      <div
        id={`faq-${index}`}
        className={`grid transition-all duration-500 ease-in-out ${open ? "grid-rows-[1fr] opacity-100" : "grid-rows-[0fr] opacity-0"}`}
      >
        <div className="overflow-hidden">
          <p className="px-6 pb-6 leading-relaxed text-[#5B5249]">{item.a}</p>
        </div>
      </div>
    </div>
  );
}

function FAQ() {
  const [open, setOpen] = useState(0);
  return (
    <section id="faq" className="scroll-mt-20 pb-24 sm:pb-32">
      <div className="mx-auto grid max-w-7xl gap-12 px-5 sm:px-8 lg:grid-cols-5 lg:gap-16">
        <div className="lg:col-span-2">
          <Reveal>
            <SectionHeading
              center={false}
              eyebrow="Good to know"
              title={
                <>
                  Questions, <em className="text-[#F28500]">answered.</em>
                </>
              }
              subtitle="Can't find what you're looking for? Message us on WhatsApp and a real person will reply."
            />
            <WhatsAppButton variant="ghost" className="mt-8" message={`Hi ${BRAND}! I have a question.`}>
              Ask us anything
            </WhatsAppButton>
          </Reveal>
        </div>
        <div className="space-y-3 lg:col-span-3">
          {FAQS.map((item, i) => (
            <Reveal key={item.q} delay={i * 60}>
              <FAQItem item={item} index={i} open={open === i} onToggle={() => setOpen(open === i ? -1 : i)} />
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function Footer() {
  return (
    <footer className="bg-[#1F1B16] text-[#E9E1D3]">
      <div className="mx-auto max-w-7xl px-5 pt-20 sm:px-8">
        <div className="grid gap-12 pb-16 md:grid-cols-2 lg:grid-cols-4">
          <div className="lg:col-span-2">
            <div className="flex items-center gap-2.5">
              <span className="grid h-10 w-10 place-items-center rounded-full bg-[#F28500] text-white">
                <Crown className="h-5 w-5" />
              </span>
              <span className="font-display text-3xl text-[#FDFBF7]">{BRAND}</span>
            </div>
            <p className="mt-5 max-w-sm leading-relaxed text-[#E9E1D3]/65">
              A Deccan dessert house for royal Qubani, Apricot Delight, fudgy chocolate brownies and pure-ghee
              halwa. Made by hand, in small batches, with a lot of love.
            </p>
            <div className="mt-6 flex gap-3">
              <a
                href="https://instagram.com"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Instagram"
                className="grid h-11 w-11 place-items-center rounded-full border border-white/10 transition-all duration-300 hover:-translate-y-0.5 hover:border-[#F28500] hover:bg-[#F28500] hover:text-white"
              >
                <Instagram className="h-4 w-4" />
              </a>
              <a
                href={waLink(DEFAULT_WA_MESSAGE)}
                target="_blank"
                rel="noopener noreferrer"
                aria-label="WhatsApp"
                className="grid h-11 w-11 place-items-center rounded-full border border-white/10 transition-all duration-300 hover:-translate-y-0.5 hover:border-[#F28500] hover:bg-[#F28500] hover:text-white"
              >
                <MessageCircle className="h-4 w-4" />
              </a>
            </div>
          </div>

          <div>
            <h4 className="text-xs font-semibold uppercase tracking-[0.25em] text-[#E9B949]">Explore</h4>
            <ul className="mt-5 space-y-3">
              {ROUTES.map((r) => (
                <li key={r.id}>
                  <a href={`#${r.id}`} className="text-[#E9E1D3]/70 transition-colors hover:text-[#F7A23B]">
                    {r.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="text-xs font-semibold uppercase tracking-[0.25em] text-[#E9B949]">Visit & order</h4>
            <ul className="mt-5 space-y-4 text-[#E9E1D3]/70">
              {LOCATIONS.map((loc) => (
                <li key={loc.city} className="flex gap-3">
                  <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-[#F28500]" />
                  <a href={loc.map} target="_blank" rel="noopener noreferrer" className="transition-colors hover:text-[#F7A23B]">
                    <span className="block font-semibold text-[#FDFBF7]">{loc.city}</span>
                    {loc.address}
                  </a>
                </li>
              ))}
              <li className="flex gap-3">
                <Clock className="mt-0.5 h-4 w-4 shrink-0 text-[#F28500]" />
                Daily · 10:00 am – 10:00 pm
              </li>
              <li className="flex gap-3">
                <Phone className="mt-0.5 h-4 w-4 shrink-0 text-[#F28500]" />
                <a href={`tel:+${WHATSAPP_NUMBER}`} className="transition-colors hover:text-[#F7A23B]">
                  {PHONE_DISPLAY} (call & WhatsApp)
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div className="flex flex-col items-center justify-between gap-4 border-t border-white/10 py-8 text-sm text-[#E9E1D3]/50 sm:flex-row">
          <p>
            © {new Date().getFullYear()} {BRAND}. All rights reserved.
          </p>
          <p className="flex items-center gap-1.5">
            Made with <Heart className="h-3.5 w-3.5 fill-[#F28500] text-[#F28500]" /> in the Deccan
          </p>
        </div>
      </div>
    </footer>
  );
}

function FloatingWhatsApp() {
  const [show, setShow] = useState(false);
  useEffect(() => {
    const onScroll = () => setShow(window.scrollY > 600);
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);
  return (
    <a
      href={waLink(DEFAULT_WA_MESSAGE)}
      target="_blank"
      rel="noopener noreferrer"
      aria-label="Order on WhatsApp"
      className={`fixed bottom-5 right-5 z-40 flex items-center gap-2 rounded-full bg-[#25D366] p-4 text-white shadow-[0_15px_35px_-10px_rgba(37,211,102,0.7)] transition-all duration-500 hover:scale-105 sm:bottom-8 sm:right-8 ${
        show ? "translate-y-0 opacity-100" : "pointer-events-none translate-y-6 opacity-0"
      }`}
    >
      <span className="absolute inset-0 animate-ping rounded-full bg-[#25D366] opacity-20" />
      <MessageCircle className="relative h-6 w-6" />
    </a>
  );
}

function ProductCard({ product, categoryName, delay = 0 }) {
  const [optIndex, setOptIndex] = useState(0);
  const [qty, setQty] = useState(1);
  const option = product.options[optIndex];
  const total = option.price * qty;
  const message = `Hi ${BRAND}! I'd like to order ${qty} × ${product.name} (${option.label}) = ${formatINR(
    total
  )}. Please share delivery details.`;

  return (
    <Reveal delay={delay} className="h-full">
      <article
        className={`group relative flex h-full flex-col overflow-hidden rounded-[2rem] bg-white transition-all duration-500 hover:-translate-y-2 ${
          product.premium
            ? "shadow-[0_40px_80px_-35px_rgba(242,133,0,0.55)] ring-2 ring-[#F28500]"
            : "shadow-[0_25px_60px_-35px_rgba(31,27,22,0.35)] ring-1 ring-[#1F1B16]/5 hover:shadow-[0_35px_70px_-35px_rgba(31,27,22,0.45)]"
        }`}
      >
        <div className="relative overflow-hidden">
          <img
            src={product.image}
            alt={`${product.name}, ${categoryName}`}
            className={`aspect-[16/11] w-full object-cover transition-transform duration-[1.2s] ease-out group-hover:scale-110 ${
              product.imagePos || ""
            }`}
          />
          <div className="absolute inset-0 bg-gradient-to-t from-[#1F1B16]/55 to-transparent" />
          <span
            className={`absolute left-5 top-5 inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider shadow-lg ${
              product.premium
                ? "bg-[#F28500] text-white"
                : product.seasonal
                ? "bg-[#1F1B16] text-[#FDFBF7]"
                : "bg-[#FDFBF7] text-[#1F1B16]"
            }`}
          >
            {product.premium ? (
              <Star className="h-3 w-3 fill-white" />
            ) : product.seasonal ? (
              <Snowflake className="h-3 w-3 text-[#F7A23B]" />
            ) : (
              <Sparkles className="h-3 w-3 text-[#F28500]" />
            )}
            {product.badge}
          </span>
          <span className="absolute bottom-4 left-5 text-xs font-medium text-white/90">{option.note}</span>
        </div>

        <div className="flex flex-1 flex-col p-7">
          <div className="flex items-start justify-between gap-4">
            <h3 className="font-display text-3xl leading-tight text-[#1F1B16]">{product.name}</h3>
            <p className="font-display shrink-0 text-3xl text-[#C46A00]">{formatINR(option.price)}</p>
          </div>
          <p className="mt-3 leading-relaxed text-[#5B5249]">{product.description}</p>

          <ul className="mt-5 flex-1 space-y-2.5">
            {product.features.map((f) => (
              <li key={f} className="flex items-center gap-3 text-sm text-[#3A322A]">
                <span className="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-[#FFF1DE]">
                  <Check className="h-3 w-3 text-[#F28500]" />
                </span>
                {f}
              </li>
            ))}
          </ul>

          <div className="mt-6" role="radiogroup" aria-label={`${product.name} size`}>
            <p className="text-xs font-medium uppercase tracking-wider text-[#8C8177]">Choose size</p>
            <div className="mt-2.5 flex flex-wrap gap-2">
              {product.options.map((o, i) => (
                <button
                  key={o.label}
                  type="button"
                  role="radio"
                  aria-checked={i === optIndex}
                  onClick={() => setOptIndex(i)}
                  className={`rounded-full px-4 py-2 text-sm font-medium transition-all duration-300 ${
                    i === optIndex
                      ? "bg-[#1F1B16] text-[#FDFBF7] shadow-md"
                      : "bg-[#FDFBF7] text-[#3A322A] ring-1 ring-[#1F1B16]/10 hover:ring-[#1F1B16]/30"
                  }`}
                >
                  {o.label}
                </button>
              ))}
            </div>
          </div>

          {option.b2b && (
            <a
              href="#bulk-orders"
              className="mt-3 inline-flex items-center gap-1.5 text-xs text-[#8C8177] transition-colors hover:text-[#C46A00]"
            >
              <Hotel className="h-3.5 w-3.5" />
              Hotels & caterers: B2B rate {formatINR(option.b2b)}
              <ArrowRight className="h-3 w-3" />
            </a>
          )}

          <div className="mt-5 flex items-center justify-between rounded-2xl bg-[#FDFBF7] p-2 pl-4">
            <span className="text-xs font-medium uppercase tracking-wider text-[#8C8177]">Quantity</span>
            <div className="flex items-center gap-1">
              <button
                type="button"
                onClick={() => setQty((q) => Math.max(1, q - 1))}
                className="grid h-9 w-9 place-items-center rounded-xl bg-white text-[#1F1B16] ring-1 ring-[#1F1B16]/10 transition hover:ring-[#1F1B16]/30 disabled:opacity-40"
                disabled={qty === 1}
                aria-label="Decrease quantity"
              >
                <Minus className="h-4 w-4" />
              </button>
              <span className="w-8 text-center font-semibold text-[#1F1B16]" aria-live="polite">
                {qty}
              </span>
              <button
                type="button"
                onClick={() => setQty((q) => Math.min(50, q + 1))}
                className="grid h-9 w-9 place-items-center rounded-xl bg-white text-[#1F1B16] ring-1 ring-[#1F1B16]/10 transition hover:ring-[#1F1B16]/30"
                aria-label="Increase quantity"
              >
                <Plus className="h-4 w-4" />
              </button>
            </div>
          </div>

          <WhatsAppButton
            message={message}
            variant={product.premium ? "primary" : "dark"}
            className="mt-4 w-full !py-4"
          >
            Order Now via WhatsApp · {formatINR(total)}
          </WhatsAppButton>
        </div>
      </article>
    </Reveal>
  );
}

function PageHero({ eyebrow, title, intro, image, from, children }) {
  return (
    <section className="relative overflow-hidden pt-28 sm:pt-32">
      <div className="pointer-events-none absolute -right-40 -top-40 h-[32rem] w-[32rem] rounded-full bg-[#F28500]/15 blur-3xl" />
      <div className="relative mx-auto grid max-w-7xl items-center gap-12 px-5 pb-16 sm:px-8 lg:grid-cols-2 lg:pb-24">
        <div>
          <Reveal>
            <a href="#home" className="inline-flex items-center gap-2 text-sm text-[#8C8177] transition-colors hover:text-[#1F1B16]">
              <ArrowRight className="h-4 w-4 rotate-180" /> All desserts
            </a>
          </Reveal>
          <Reveal delay={80}>
            <div className="mt-6">
              <Eyebrow>{eyebrow}</Eyebrow>
            </div>
            <h1 className="font-display mt-5 text-5xl leading-[1] text-[#1F1B16] sm:text-7xl">{title}</h1>
          </Reveal>
          <Reveal delay={160}>
            <p className="mt-6 max-w-xl text-lg leading-relaxed text-[#5B5249]">{intro}</p>
          </Reveal>
          <Reveal delay={240}>
            <div className="mt-9 flex flex-col gap-3 sm:flex-row">{children}</div>
          </Reveal>
        </div>
        <Reveal delay={120}>
          <div className="relative mx-auto max-w-xl">
            <div className="absolute -inset-4 rounded-[3rem] border border-[#E9B949]/40" />
            <div className="relative overflow-hidden rounded-[2.5rem] shadow-[0_40px_80px_-30px_rgba(120,60,0,0.45)]">
              <img src={image} alt="" className="aspect-[5/4] w-full object-cover transition-transform duration-[1.5s] hover:scale-105" />
            </div>
            {from && (
              <div className="animate-float-slow absolute -bottom-5 right-6 rounded-2xl bg-[#1F1B16]/90 px-5 py-4 text-[#FDFBF7] shadow-xl backdrop-blur">
                <p className="text-[10px] uppercase tracking-[0.25em] text-[#E9B949]">Starting from</p>
                <p className="font-display text-3xl leading-none">{formatINR(from)}</p>
              </div>
            )}
          </div>
        </Reveal>
      </div>
    </section>
  );
}

function Highlights({ items }) {
  return (
    <div className="grid gap-4 rounded-[2rem] bg-[#FFF1DE]/60 p-6 sm:grid-cols-3 sm:p-8">
      {items.map(([Icon, title, text]) => (
        <div key={title} className="flex items-center gap-4">
          <span className="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-white text-[#F28500] shadow-sm">
            <Icon className="h-5 w-5" />
          </span>
          <div>
            <p className="font-semibold text-[#1F1B16]">{title}</p>
            <p className="text-sm text-[#8C8177]">{text}</p>
          </div>
        </div>
      ))}
    </div>
  );
}

function CategoryShowcase() {
  return (
    <section className="relative py-24 sm:py-32">
      <div className="mx-auto max-w-7xl px-5 sm:px-8">
        <Reveal>
          <SectionHeading
            eyebrow="Our desserts"
            title={
              <>
                Three kitchens of <em className="text-[#F28500]">indulgence.</em>
              </>
            }
            subtitle="Pick a category to see every dessert, size and price, then order in one tap on WhatsApp."
          />
        </Reveal>
        <div className="mt-16 grid gap-6 md:grid-cols-3">
          {CATEGORIES.map((c, i) => (
            <Reveal key={c.id} delay={i * 120} className="h-full">
              <a
                href={`#${c.id}`}
                className="group relative flex h-full min-h-[30rem] flex-col justify-end overflow-hidden rounded-[2rem] shadow-[0_30px_70px_-35px_rgba(31,27,22,0.55)]"
              >
                <img
                  src={c.image}
                  alt={c.name}
                  className="absolute inset-0 h-full w-full object-cover transition-transform duration-[1.2s] ease-out group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#1F1B16] from-15% via-[#1F1B16]/80 via-50% to-[#1F1B16]/0" />
                <div className="relative p-7 text-[#FDFBF7]">
                  <p className="text-[11px] font-semibold uppercase tracking-[0.28em] text-[#F7C27A]">0{i + 1}</p>
                  <h3 className="font-display mt-2 text-4xl">{c.name}</h3>
                  <p className="mt-2 text-sm leading-relaxed text-[#E9E1D3]/80">{c.cardText}</p>
                  <ul className="mt-4 flex flex-wrap gap-2">
                    {c.products.map((p) => (
                      <li key={p.id} className="rounded-full bg-white/10 px-3 py-1 text-xs backdrop-blur">
                        {p.name}
                      </li>
                    ))}
                  </ul>
                  <div className="mt-6 flex items-center justify-between border-t border-white/15 pt-5">
                    <span className="text-sm text-[#E9E1D3]/80">
                      From <strong className="font-display text-2xl font-medium text-white">{formatINR(fromPrice(c))}</strong>
                    </span>
                    <span className="grid h-11 w-11 place-items-center rounded-full bg-[#F28500] text-white transition-transform duration-300 group-hover:translate-x-1">
                      <ArrowRight className="h-5 w-5" />
                    </span>
                  </div>
                </div>
              </a>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function SignatureSpotlight() {
  return (
    <section className="relative overflow-hidden pb-24 sm:pb-32">
      <div className="mx-auto grid max-w-7xl items-center gap-14 px-5 sm:px-8 lg:grid-cols-2 lg:gap-20">
        <Reveal>
          <div className="relative mx-auto grid max-w-lg grid-cols-2 gap-4">
            <img src={IMAGES.slice} alt="Apricot Delight slice with fresh cream and almonds" className="aspect-[3/4] w-full rounded-[2rem] object-cover shadow-xl" />
            <img src={IMAGES.qubaniClassicTall} alt="Classic Qubani ka Meetha in a glass bowl" className="mt-12 aspect-[3/4] w-full rounded-[2rem] object-cover shadow-xl" />
          </div>
        </Reveal>
        <div>
          <Reveal>
            <SectionHeading
              center={false}
              eyebrow="The signature"
              title={
                <>
                  Classic Qubani, or the <em className="text-[#F28500]">Apricot Delight?</em>
                </>
              }
              subtitle="Our Qubani comes two ways. The classic is the heirloom dessert, glossy stewed apricots with cream. Apricot Delight is its premium avatar: the same Qubani layered with butter sponge and fresh whipped cream into a celebration cake."
            />
          </Reveal>
          <Reveal delay={120}>
            <div className="mt-8 grid grid-cols-2 gap-4">
              {CATEGORIES[0].products.map((p) => (
                <div key={p.id} className={`rounded-2xl p-5 ${p.premium ? "bg-[#1F1B16] text-[#FDFBF7]" : "bg-white ring-1 ring-[#1F1B16]/10"}`}>
                  <p className={`text-[10px] font-semibold uppercase tracking-[0.25em] ${p.premium ? "text-[#E9B949]" : "text-[#C46A00]"}`}>{p.badge}</p>
                  <p className="font-display mt-1 text-2xl">{p.name}</p>
                  <p className={`mt-1 text-sm ${p.premium ? "text-[#E9E1D3]/70" : "text-[#8C8177]"}`}>From {formatINR(p.options[0].price)}</p>
                </div>
              ))}
            </div>
            <a
              href="#qubani"
              className="group mt-8 inline-flex items-center gap-2 rounded-full bg-[#1F1B16] px-6 py-3.5 text-sm font-semibold text-[#FDFBF7] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#3A322A]"
            >
              Explore Qubani
              <ArrowRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
            </a>
          </Reveal>
        </div>
      </div>
    </section>
  );
}

function QubaniComparison() {
  const rows = [
    ["Qubani compote", true, true],
    ["Fresh cream topping", true, true],
    ["Almond & pistachio garnish", true, true],
    ["Moist butter sponge layers", false, true],
    ["Hand-piped whipped cream crown", false, true],
    ["Sliceable celebration cake", false, true],
  ];
  return (
    <section className="py-20 sm:py-24">
      <div className="mx-auto max-w-4xl px-5 sm:px-8">
        <Reveal>
          <SectionHeading
            eyebrow="Compare"
            title={
              <>
                Classic vs <em className="text-[#F28500]">Apricot Delight</em>
              </>
            }
            subtitle="Same royal Qubani at heart. Choose the bowl-and-spoon classic, or the layered premium cake."
          />
        </Reveal>
        <Reveal delay={100}>
          <div className="mt-12 overflow-hidden rounded-[2rem] bg-white shadow-[0_30px_70px_-40px_rgba(31,27,22,0.45)] ring-1 ring-[#1F1B16]/5">
            <div className="grid grid-cols-[1.6fr_1fr_1fr] items-end border-b border-[#1F1B16]/10 px-5 py-5 sm:px-8">
              <span className="text-xs uppercase tracking-wider text-[#8C8177]">What you get</span>
              <span className="font-display text-center text-xl text-[#1F1B16] sm:text-2xl">Classic</span>
              <span className="font-display text-center text-xl text-[#C46A00] sm:text-2xl">Delight</span>
            </div>
            {rows.map(([label, a, b]) => (
              <div key={label} className="grid grid-cols-[1.6fr_1fr_1fr] items-center border-b border-[#1F1B16]/5 px-5 py-4 text-sm last:border-0 sm:px-8">
                <span className="text-[#3A322A]">{label}</span>
                {[a, b].map((v, i) => (
                  <span key={i} className="flex justify-center">
                    {v ? (
                      <span className={`grid h-7 w-7 place-items-center rounded-full ${i ? "bg-[#F28500] text-white" : "bg-[#FFF1DE] text-[#F28500]"}`}>
                        <Check className="h-4 w-4" />
                      </span>
                    ) : (
                      <Minus className="h-4 w-4 text-[#1F1B16]/20" />
                    )}
                  </span>
                ))}
              </div>
            ))}
          </div>
        </Reveal>
      </div>
    </section>
  );
}

function WinterSpecial({ showLink = true }) {
  return (
    <section className="px-5 pb-24 sm:px-8">
      <Reveal>
        <div className="relative mx-auto grid max-w-7xl overflow-hidden rounded-[2.5rem] bg-[#1F1B16] text-[#FDFBF7] lg:grid-cols-2">
          <div className="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-[#F28500]/25 blur-3xl" />
          <div className="relative p-8 sm:p-14">
            <span className="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#F7C27A]">
              <Snowflake className="h-3.5 w-3.5" /> Winter special
            </span>
            <h2 className="font-display mt-6 text-4xl leading-[1.05] sm:text-6xl">
              Gajar ka Halwa is <em className="text-[#F7A23B]">back.</em>
            </h2>
            <p className="mt-5 max-w-md text-lg leading-relaxed text-[#E9E1D3]/80">
              Made only in the winter months, with fresh red carrots slow-cooked in milk and ghee and loaded with dry
              fruits. Small batches, so order early.
            </p>
            <p className="mt-6 text-sm text-[#E9E1D3]/70">
              From <strong className="font-display text-3xl font-medium text-white">{formatINR(findProduct("gajar-halwa").options[0].price)}</strong> · 250 g
            </p>
            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
              <WhatsAppButton message={`Hi ${BRAND}! I'd like to order Gajar ka Halwa (winter special).`}>
                Order Gajar Halwa
              </WhatsAppButton>
              {showLink && (
                <a
                  href="#halwa"
                  className="group inline-flex items-center justify-center gap-2 rounded-full border border-white/20 px-6 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 hover:bg-white/10"
                >
                  See all halwas
                  <ArrowRight className="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
                </a>
              )}
            </div>
          </div>
          <div className="relative min-h-[18rem]">
            <img
              src={IMAGES.gajarCups}
              alt="Gajar ka halwa in takeaway boxes and bowls topped with cashews, almonds and pistachio"
              className="absolute inset-0 h-full w-full object-cover transition-transform duration-[1.5s] hover:scale-105"
            />
            <div className="absolute inset-0 bg-gradient-to-r from-[#1F1B16] via-transparent to-transparent lg:via-[#1F1B16]/10" />
          </div>
        </div>
      </Reveal>
    </section>
  );
}

const HALWA_GALLERY = [
  { src: IMAGES.halwa, alt: "Badam halwa in a brass bowl with almonds and silver vark", label: "Badam Halwa", span: "sm:row-span-2" },
  { src: IMAGES.kaju, alt: "Kaju halwa in a blue gift tin with squares on a plate", label: "Kaju Halwa · Gift tin" },
  { src: IMAGES.gajarTray, alt: "Gajar ka halwa in a box on a dark brass tray", label: "Gajar ka Halwa", span: "sm:col-span-2" },
  { src: IMAGES.kajuPlate, alt: "Kaju halwa squares with cashews beside an open gift tin", label: "Kaju Halwa" },
  { src: IMAGES.gajar, alt: "Gajar ka halwa topped with cashews and pistachio on a brass tray", label: "Gajar ka Halwa · Takeaway" },
  { src: IMAGES.gajarCups, alt: "Gajar ka halwa in labelled boxes and bowls", label: "Party portions", span: "sm:col-span-2" },
];

function Gallery({ eyebrow, title, items }) {
  const [open, setOpen] = useState(-1);
  const close = () => setOpen(-1);
  const step = (d) => setOpen((i) => (i + d + items.length) % items.length);

  useEffect(() => {
    if (open < 0) return;
    const onKey = (e) => {
      if (e.key === "Escape") close();
      if (e.key === "ArrowRight") step(1);
      if (e.key === "ArrowLeft") step(-1);
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open]);

  return (
    <section className="pb-24 sm:pb-28">
      <div className="mx-auto max-w-7xl px-5 sm:px-8">
        <Reveal>
          <SectionHeading eyebrow={eyebrow} title={title} subtitle="Tap any photo to see it larger." />
        </Reveal>
        <div className="mt-12 grid auto-rows-[14rem] grid-cols-2 gap-4 sm:auto-rows-[16rem] sm:grid-flow-dense sm:grid-cols-3">
          {items.map((item, i) => (
            <Reveal key={item.src + i} delay={i * 70} className={`h-full ${item.span || ""}`}>
              <button
                type="button"
                onClick={() => setOpen(i)}
                className="group relative block h-full w-full overflow-hidden rounded-[1.75rem] text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F28500]"
                aria-label={`Open photo: ${item.label}`}
              >
                <img
                  src={item.src}
                  alt={item.alt}
                  className="h-full w-full object-cover transition-transform duration-[1.2s] ease-out group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#1F1B16]/70 via-transparent to-transparent opacity-80 transition-opacity duration-300 group-hover:opacity-100" />
                <span className="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-2 text-sm font-medium text-white">
                  {item.label}
                  <span className="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-white/20 opacity-0 backdrop-blur transition-all duration-300 group-hover:opacity-100">
                    <Plus className="h-4 w-4" />
                  </span>
                </span>
              </button>
            </Reveal>
          ))}
        </div>
      </div>

      {open >= 0 && (
        <div
          role="dialog"
          aria-modal="true"
          aria-label={items[open].label}
          onClick={close}
          className="animate-fade-up fixed inset-0 z-[60] flex items-center justify-center bg-[#1F1B16]/90 p-4 backdrop-blur-sm sm:p-10"
        >
          <figure className="relative max-h-full max-w-5xl" onClick={(e) => e.stopPropagation()}>
            <img src={items[open].src} alt={items[open].alt} className="max-h-[80vh] w-auto rounded-3xl object-contain shadow-2xl" />
            <figcaption className="mt-4 text-center text-sm text-[#E9E1D3]/80">
              {items[open].label} · {open + 1} / {items.length}
            </figcaption>
          </figure>
          <button onClick={close} aria-label="Close" className="absolute right-4 top-4 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20">
            <X className="h-5 w-5" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); step(-1); }}
            aria-label="Previous photo"
            className="absolute left-3 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20 sm:left-6"
          >
            <ArrowRight className="h-5 w-5 rotate-180" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); step(1); }}
            aria-label="Next photo"
            className="absolute right-3 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20 sm:right-6"
          >
            <ArrowRight className="h-5 w-5" />
          </button>
        </div>
      )}
    </section>
  );
}

function B2BPriceList() {
  return (
    <section className="pb-24">
      <div className="mx-auto max-w-5xl px-5 sm:px-8">
        <Reveal>
          <SectionHeading
            eyebrow="Trade pricing"
            title={
              <>
                B2B price list for <em className="text-[#F28500]">hotels & caterers.</em>
              </>
            }
            subtitle="Special rates for hotels, restaurants, caterers and event planners. Retail prices are shown alongside for comparison."
          />
        </Reveal>
        <div className="mt-12 space-y-6">
          {CATEGORIES.map((c, ci) => (
            <Reveal key={c.id} delay={ci * 80}>
              <div className="overflow-hidden rounded-[2rem] bg-white shadow-[0_25px_60px_-40px_rgba(31,27,22,0.4)] ring-1 ring-[#1F1B16]/5">
                <div className="flex items-center gap-4 border-b border-[#1F1B16]/10 bg-[#FDFBF7] px-5 py-4 sm:px-8">
                  <img src={c.image} alt="" className="h-12 w-12 rounded-xl object-cover" />
                  <h3 className="font-display text-2xl text-[#1F1B16] sm:text-3xl">{c.name}</h3>
                </div>
                <div className="overflow-x-auto">
                  <table className="w-full min-w-[30rem] table-fixed text-left text-sm">
                    <thead>
                      <tr className="text-xs uppercase tracking-wider text-[#8C8177]">
                        <th className="w-[40%] px-5 py-3 font-medium sm:px-8">Item</th>
                        <th className="w-[20%] px-3 py-3 font-medium">Size</th>
                        <th className="px-3 py-3 text-right font-medium text-[#C46A00]">B2B</th>
                        <th className="px-5 py-3 text-right font-medium sm:px-8">Retail</th>
                      </tr>
                    </thead>
                    <tbody>
                      {c.products.flatMap((p) =>
                        p.options.map((o, oi) => (
                          <tr key={p.id + o.label} className={`border-t border-[#1F1B16]/5 ${oi === 0 ? "border-[#1F1B16]/10" : ""}`}>
                            <td className="px-5 py-3 font-medium text-[#1F1B16] sm:px-8">{oi === 0 ? p.name : ""}</td>
                            <td className="px-3 py-3 text-[#5B5249]">{o.label}</td>
                            <td className="px-3 py-3 text-right font-semibold text-[#C46A00]">
                              {o.b2b ? formatINR(o.b2b) : <span className="font-normal text-[#8C8177]">On request</span>}
                            </td>
                            <td className="px-5 py-3 text-right text-[#5B5249] sm:px-8">{formatINR(o.price)}</td>
                          </tr>
                        ))
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            </Reveal>
          ))}
        </div>
        <Reveal>
          <div className="mt-8 flex flex-col items-center gap-4 text-center">
            <p className="text-sm text-[#8C8177]">
              B2B rates apply to registered businesses and regular bulk orders. Prices include packaging; delivery is quoted separately.
            </p>
            <WhatsAppButton message={`Hi ${BRAND}! I'm a business (hotel / restaurant / caterer) and would like your B2B rates and order terms.`}>
              Request B2B account
            </WhatsAppButton>
          </div>
        </Reveal>
      </div>
    </section>
  );
}

function CrossSell({ current }) {
  const others = CATEGORIES.filter((c) => c.id !== current);
  return (
    <section className="pb-24 sm:pb-28">
      <div className="mx-auto max-w-7xl px-5 sm:px-8">
        <Reveal>
          <h2 className="font-display text-3xl text-[#1F1B16] sm:text-4xl">You might also love</h2>
        </Reveal>
        <div className="mt-8 grid gap-6 sm:grid-cols-2">
          {others.map((c, i) => (
            <Reveal key={c.id} delay={i * 100}>
              <a
                href={`#${c.id}`}
                className="group flex items-center gap-5 rounded-[2rem] bg-white p-4 pr-6 shadow-[0_25px_60px_-40px_rgba(31,27,22,0.4)] ring-1 ring-[#1F1B16]/5 transition-all duration-500 hover:-translate-y-1"
              >
                <img src={c.image} alt={c.name} className="h-24 w-24 shrink-0 rounded-2xl object-cover sm:h-28 sm:w-28" />
                <div className="flex-1">
                  <p className="font-display text-2xl text-[#1F1B16] sm:text-3xl">{c.name}</p>
                  <p className="mt-1 text-sm text-[#8C8177]">From {formatINR(fromPrice(c))}</p>
                </div>
                <ArrowRight className="h-5 w-5 text-[#C46A00] transition-transform duration-300 group-hover:translate-x-1" />
              </a>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

function CategoryPage({ category }) {
  return (
    <>
      <PageHero
        eyebrow={category.eyebrow}
        title={category.title}
        intro={category.intro}
        image={category.image}
        from={fromPrice(category)}
      >
        <WhatsAppButton message={`Hi ${BRAND}! I'd like to order from your ${category.name} menu.`}>
          Order on WhatsApp
        </WhatsAppButton>
        <a
          href="#bulk-orders"
          className="inline-flex items-center justify-center gap-2 rounded-full border border-[#1F1B16]/15 bg-white/60 px-6 py-3.5 text-sm font-semibold text-[#1F1B16] transition-all duration-300 hover:-translate-y-0.5 hover:bg-white"
        >
          <Users className="h-4 w-4" /> Bulk & event orders
        </a>
      </PageHero>

      <section className="pb-12">
        <div className="mx-auto max-w-7xl px-5 sm:px-8">
          <Reveal>
            <Highlights items={category.highlights} />
          </Reveal>
        </div>
      </section>

      <section className="py-16 sm:py-20">
        <div className="mx-auto max-w-7xl px-5 sm:px-8">
          <Reveal>
            <SectionHeading eyebrow="The menu" title={<>Choose, size up, <em className="text-[#F28500]">order.</em></>} />
          </Reveal>
          <div
            className={`mx-auto mt-14 grid gap-8 md:grid-cols-2 ${
              category.products.length > 2 ? "max-w-7xl lg:grid-cols-3" : "max-w-5xl"
            }`}
          >
            {category.products.map((p, i) => (
              <ProductCard key={p.id} product={p} categoryName={category.name} delay={i * 120} />
            ))}
          </div>
        </div>
      </section>

      {category.id === "halwa" && (
        <>
          <WinterSpecial showLink={false} />
          <Gallery
            eyebrow="Gallery"
            title={
              <>
                A closer look at our <em className="text-[#F28500]">halwas.</em>
              </>
            }
            items={HALWA_GALLERY}
          />
        </>
      )}

      {category.id === "qubani" && (
        <>
          <QubaniComparison />
          <LayerBreakdown />
        </>
      )}

      <CrossSell current={category.id} />
    </>
  );
}

function BulkOrdersPage() {
  return (
    <>
      <div className="pt-20 sm:pt-24" />
      <EventsSection />
      <B2BPriceList />
      <section className="pb-24">
        <div className="mx-auto max-w-7xl px-5 sm:px-8">
          <Reveal>
            <SectionHeading
              eyebrow="Our kitchens"
              title={
                <>
                  Visit or order from <em className="text-[#F28500]">two cities.</em>
                </>
              }
            />
          </Reveal>
          <div className="mt-12 grid gap-6 md:grid-cols-2">
            {LOCATIONS.map((loc, i) => (
              <Reveal key={loc.city} delay={i * 120}>
                <div className="flex h-full flex-col rounded-[2rem] bg-white p-8 shadow-[0_25px_60px_-40px_rgba(31,27,22,0.4)] ring-1 ring-[#1F1B16]/5">
                  <span className="grid h-12 w-12 place-items-center rounded-2xl bg-[#FFF1DE] text-[#F28500]">
                    <MapPin className="h-5 w-5" />
                  </span>
                  <h3 className="font-display mt-5 text-3xl text-[#1F1B16]">{loc.city}</h3>
                  <p className="mt-2 flex-1 text-[#5B5249]">{loc.address}</p>
                  <div className="mt-6 flex flex-wrap gap-3">
                    <a
                      href={loc.map}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-flex items-center gap-2 rounded-full bg-[#1F1B16] px-5 py-3 text-sm font-semibold text-[#FDFBF7] transition hover:bg-[#3A322A]"
                    >
                      <MapPin className="h-4 w-4" /> Get directions
                    </a>
                    <a
                      href={`tel:+${WHATSAPP_NUMBER}`}
                      className="inline-flex items-center gap-2 rounded-full border border-[#1F1B16]/15 px-5 py-3 text-sm font-semibold text-[#1F1B16] transition hover:bg-[#FDFBF7]"
                    >
                      <Phone className="h-4 w-4" /> {PHONE_DISPLAY}
                    </a>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        </div>
      </section>
      <FAQ />
    </>
  );
}

function HomePage() {
  return (
    <>
      <Hero />
      <CategoryShowcase />
      <WinterSpecial />
      <SignatureSpotlight />
      <Story />
      <Reviews />
      <EventsSection showCards={false} />
      <FAQ />
    </>
  );
}

/* ------------------------------------------------------------------ */
/*  Global styles (fonts + keyframes)                                  */
/* ------------------------------------------------------------------ */

const GLOBAL_CSS = `
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap');
html { scroll-behavior: smooth; }
body { margin: 0; background: #FDFBF7; }
.qa-root { font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif; }
.font-display { font-family: 'Cormorant Garamond', ui-serif, Georgia, serif; font-weight: 500; letter-spacing: -0.01em; }
.font-display em { font-style: italic; }
@keyframes qa-marquee { from { transform: translateX(0); } to { transform: translateX(-100%); } }
@keyframes qa-float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
@keyframes qa-fade-up { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.animate-marquee { animation: qa-marquee 40s linear infinite; }
.animate-float { animation: qa-float 6s ease-in-out infinite; }
.animate-float-slow { animation: qa-float 8s ease-in-out 1s infinite; }
.animate-fade-up { animation: qa-fade-up .5s ease-out both; }
@media (prefers-reduced-motion: reduce) {
  html { scroll-behavior: auto; }
  .animate-marquee, .animate-float, .animate-float-slow, .animate-fade-up { animation: none; }
}
`;

/* ------------------------------------------------------------------ */
/*  Page                                                               */
/* ------------------------------------------------------------------ */

export default function QubaniLanding() {
  const route = useHashRoute();
  const category = CATEGORIES.find((c) => c.id === route);

  return (
    <div className="qa-root min-h-screen overflow-x-hidden bg-[#FDFBF7] text-[#1F1B16] antialiased selection:bg-[#F28500] selection:text-white">
      <style>{GLOBAL_CSS}</style>
      <Navbar route={route} />
      <main key={route}>
        {route === "home" && <HomePage />}
        {category && <CategoryPage category={category} />}
        {route === "bulk-orders" && <BulkOrdersPage />}
      </main>
      <Footer />
      <FloatingWhatsApp />
    </div>
  );
}
