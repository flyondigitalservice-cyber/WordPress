import React, { useEffect, useRef, useState } from "react";
import {
  ArrowRight,
  Award,
  Check,
  ChevronDown,
  Clock,
  Crown,
  Gift,
  Heart,
  Instagram,
  Leaf,
  MapPin,
  Menu,
  MessageCircle,
  Minus,
  Phone,
  Plus,
  Quote,
  ShieldCheck,
  Sparkles,
  Star,
  Truck,
  X,
} from "lucide-react";

/* ------------------------------------------------------------------ */
/*  Config — change these to your own details                          */
/* ------------------------------------------------------------------ */

const BRAND = "Qubani Atelier";
const WHATSAPP_NUMBER = "919999999999"; // country code + number, digits only
const PHONE_DISPLAY = "+91 99999 99999";

const IMAGES = {
  hero: "./images/layer-cake-slicing-on-plate.jpg",
  slice: "./images/apricot-dessert-slice-with-cream.jpg",
  bowl: "./images/apricot-dessert-in-bowl.jpg",
  scoop: "./images/hand-scooping-apricot-dessert.jpg",
  tray: "./images/layered-apricot-dessert-in-bowl.jpg",
};

const waLink = (message) =>
  `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;

const DEFAULT_WA_MESSAGE = `Hi ${BRAND}! I'd like to order the Royal Qubani Cake.`;

/* ------------------------------------------------------------------ */
/*  Content                                                            */
/* ------------------------------------------------------------------ */

const NAV_LINKS = [
  { label: "Our Story", href: "#story" },
  { label: "The Layers", href: "#layers" },
  { label: "Menu", href: "#menu" },
  { label: "Reviews", href: "#reviews" },
  { label: "FAQ", href: "#faq" },
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

const PRODUCTS = [
  {
    id: "half",
    name: "Half KG",
    subtitle: "For an intimate evening",
    price: 749,
    serves: "Serves 4–5",
    image: IMAGES.bowl,
    features: ["Signature three-layer build", "Almond & pistachio crown", "Chilled insulated box"],
  },
  {
    id: "one",
    name: "1 KG",
    subtitle: "The family favourite",
    price: 1399,
    serves: "Serves 8–10",
    image: IMAGES.hero,
    features: ["Extra-thick Qubani heart", "Hand-piped fresh cream", "Personal message tag", "Free delivery in city"],
    popular: true,
  },
  {
    id: "gift",
    name: "Festive Gift Box",
    subtitle: "Eid, Diwali & weddings",
    price: 1899,
    serves: "1 KG + extras",
    image: IMAGES.tray,
    features: ["Keepsake gold-foil box", "Jar of Qubani compote", "Handwritten greeting card", "Silk ribbon finish"],
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
    place: "Pune",
    text: "Sent the festive gift box to my in-laws for Diwali. The packaging alone got compliments, and the cake disappeared in ten minutes flat.",
  },
  {
    name: "Fatima Khan",
    place: "Hyderabad",
    text: "As a Hyderabadi I was sceptical. I'm not anymore. The apricots are proper slow-stewed, not that jelly shortcut. Easily the best fusion dessert I've had.",
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
    q: "Is the cake eggless?",
    a: "Our classic sponge contains eggs. An eggless version is available on request at no extra cost — just mention it in your WhatsApp message.",
  },
  {
    q: "How should I store it, and how long does it last?",
    a: "Keep it refrigerated and enjoy within 48 hours. Take it out 10 minutes before serving so the cream softens and the Qubani becomes glossy again.",
  },
  {
    q: "Do you deliver outside the city?",
    a: "We deliver across the city in chilled boxes. For out-of-city orders, our Qubani compote jars travel beautifully — ask us about courier options.",
  },
];

const INGREDIENTS = [
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

/* ------------------------------------------------------------------ */
/*  Sections                                                           */
/* ------------------------------------------------------------------ */

function Navbar() {
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <header
      className={`fixed inset-x-0 top-0 z-50 transition-all duration-500 ${
        scrolled || open
          ? "bg-[#FDFBF7]/85 shadow-[0_1px_0_rgba(31,27,22,0.06)] backdrop-blur-xl"
          : "bg-transparent"
      }`}
    >
      <nav className="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8">
        <a href="#top" className="group flex items-center gap-2.5" aria-label={`${BRAND} home`}>
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

        <ul className="hidden items-center gap-9 lg:flex">
          {NAV_LINKS.map((l) => (
            <li key={l.href}>
              <a
                href={l.href}
                className="relative text-sm font-medium text-[#3A322A] transition-colors hover:text-[#1F1B16] after:absolute after:-bottom-1.5 after:left-0 after:h-px after:w-0 after:bg-[#F28500] after:transition-all after:duration-300 hover:after:w-full"
              >
                {l.label}
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
        className={`overflow-hidden transition-[max-height] duration-500 lg:hidden ${open ? "max-h-[28rem]" : "max-h-0"}`}
      >
        <ul className="space-y-1 px-5 pb-6">
          {NAV_LINKS.map((l) => (
            <li key={l.href}>
              <a
                href={l.href}
                onClick={() => setOpen(false)}
                className="flex items-center justify-between rounded-2xl px-4 py-3.5 text-base font-medium text-[#1F1B16] hover:bg-[#F6EEDD]"
              >
                {l.label}
                <ArrowRight className="h-4 w-4 text-[#C46A00]" />
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
              Heritage recipe · Baked fresh every morning
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
              Half heirloom dessert, half celebration cake. Slow-stewed, glossy apricot compote from the royal
              kitchens of the Deccan, layered between a butter-soft sponge and a cloud of fresh cream, crowned with
              toasted almonds.
            </p>
          </Reveal>

          <Reveal delay={300}>
            <div className="mt-10 flex flex-col gap-3 sm:flex-row">
              <WhatsAppButton>Order on WhatsApp</WhatsAppButton>
              <a
                href="#layers"
                className="group inline-flex items-center justify-center gap-2 rounded-full border border-[#1F1B16]/15 bg-white/60 px-6 py-3.5 text-sm font-semibold text-[#1F1B16] backdrop-blur transition-all duration-300 hover:-translate-y-0.5 hover:border-[#1F1B16]/40 hover:bg-white"
              >
                Explore the layers
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
                  alt="A slice of Royal Qubani layer cake with fresh cream, apricot compote and almonds on a marble plate"
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
                <p className="text-[10px] uppercase tracking-[0.25em] text-[#E9B949]">From</p>
                <p className="font-display text-3xl leading-none">{formatINR(749)}</p>
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
            eyebrow="Anatomy of a slice"
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

function ProductCard({ product, delay }) {
  const [qty, setQty] = useState(1);
  const message = `Hi ${BRAND}! I'd like to order ${qty} × ${product.name} Royal Qubani Cake (${formatINR(
    product.price * qty
  )}). Please share delivery details.`;

  return (
    <Reveal delay={delay} className="h-full">
      <article
        className={`group relative flex h-full flex-col overflow-hidden rounded-[2rem] bg-white transition-all duration-500 hover:-translate-y-2 ${
          product.popular
            ? "shadow-[0_40px_80px_-35px_rgba(242,133,0,0.55)] ring-2 ring-[#F28500]"
            : "shadow-[0_25px_60px_-35px_rgba(31,27,22,0.35)] ring-1 ring-[#1F1B16]/5 hover:shadow-[0_35px_70px_-35px_rgba(31,27,22,0.45)]"
        }`}
      >
        <div className="relative overflow-hidden">
          <img
            src={product.image}
            alt={`${product.name} Royal Qubani Cake`}
            className="aspect-[4/3] w-full object-cover transition-transform duration-[1.2s] ease-out group-hover:scale-110"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-[#1F1B16]/50 to-transparent" />
          {product.popular && (
            <span className="absolute left-5 top-5 inline-flex items-center gap-1.5 rounded-full bg-[#F28500] px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-white shadow-lg">
              <Star className="h-3 w-3 fill-white" /> Bestseller
            </span>
          )}
          {product.id === "gift" && (
            <span className="absolute left-5 top-5 inline-flex items-center gap-1.5 rounded-full bg-[#E9B949] px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-[#1F1B16] shadow-lg">
              <Gift className="h-3 w-3" /> Most gifted
            </span>
          )}
          <span className="absolute bottom-4 left-5 text-xs font-medium text-white/90">{product.serves}</span>
        </div>

        <div className="flex flex-1 flex-col p-7">
          <div className="flex items-start justify-between gap-4">
            <div>
              <h3 className="font-display text-3xl text-[#1F1B16]">{product.name}</h3>
              <p className="mt-1 text-sm text-[#8C8177]">{product.subtitle}</p>
            </div>
            <p className="font-display text-3xl text-[#C46A00]">{formatINR(product.price)}</p>
          </div>

          <ul className="mt-6 flex-1 space-y-3">
            {product.features.map((f) => (
              <li key={f} className="flex items-center gap-3 text-sm text-[#3A322A]">
                <span className="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-[#FFF1DE]">
                  <Check className="h-3 w-3 text-[#F28500]" />
                </span>
                {f}
              </li>
            ))}
          </ul>

          <div className="mt-7 flex items-center justify-between rounded-2xl bg-[#FDFBF7] p-2 pl-4">
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
                onClick={() => setQty((q) => Math.min(20, q + 1))}
                className="grid h-9 w-9 place-items-center rounded-xl bg-white text-[#1F1B16] ring-1 ring-[#1F1B16]/10 transition hover:ring-[#1F1B16]/30"
                aria-label="Increase quantity"
              >
                <Plus className="h-4 w-4" />
              </button>
            </div>
          </div>

          <WhatsAppButton
            message={message}
            variant={product.popular ? "primary" : "dark"}
            className="mt-4 w-full !py-4"
          >
            Order Now via WhatsApp · {formatINR(product.price * qty)}
          </WhatsAppButton>
        </div>
      </article>
    </Reveal>
  );
}

function MenuSection() {
  return (
    <section id="menu" className="relative scroll-mt-20 py-24 sm:py-32">
      <div className="mx-auto max-w-7xl px-5 sm:px-8">
        <Reveal>
          <SectionHeading
            eyebrow="The menu"
            title={
              <>
                Choose your <em className="text-[#F28500]">celebration.</em>
              </>
            }
            subtitle="One signature recipe in three sizes. Pick a size, set the quantity, and we'll confirm your order on WhatsApp in minutes."
          />
        </Reveal>

        <div className="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          {PRODUCTS.map((p, i) => (
            <ProductCard key={p.id} product={p} delay={i * 120} />
          ))}
        </div>

        <Reveal delay={200}>
          <div className="mt-14 grid gap-4 rounded-[2rem] bg-[#FFF1DE]/60 p-6 sm:grid-cols-3 sm:p-8">
            {[
              [Truck, "Same-day delivery", "Order before 12 noon"],
              [ShieldCheck, "Hygienic & sealed", "Tamper-proof chilled boxes"],
              [Leaf, "Eggless on request", "No extra charge"],
            ].map(([Icon, title, text]) => (
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
        </Reveal>
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

function CtaBanner() {
  return (
    <section className="px-5 py-24 sm:px-8 sm:py-28">
      <Reveal>
        <div className="relative mx-auto max-w-7xl overflow-hidden rounded-[2.5rem] bg-[#F28500]">
          <div className="absolute inset-0 bg-gradient-to-br from-[#F7A23B] via-[#F28500] to-[#C45A00]" />
          <div className="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/15 blur-2xl" />
          <div className="relative grid items-center gap-10 p-8 sm:p-14 lg:grid-cols-5">
            <div className="lg:col-span-3">
              <p className="text-xs font-semibold uppercase tracking-[0.3em] text-white/80">Festive season is here</p>
              <h2 className="font-display mt-4 text-4xl leading-[1.05] text-white sm:text-6xl">
                Gift a little piece of royal heritage.
              </h2>
              <p className="mt-5 max-w-lg text-lg text-white/85">
                Corporate hampers, wedding favours and bulk festive orders, all beautifully boxed and hand-delivered.
              </p>
              <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                <WhatsAppButton
                  variant="dark"
                  message={`Hi ${BRAND}! I'm interested in a festive / bulk gifting order.`}
                >
                  Plan a gifting order
                </WhatsAppButton>
                <a
                  href={`tel:+${WHATSAPP_NUMBER}`}
                  className="inline-flex items-center justify-center gap-2 rounded-full border border-white/40 px-6 py-3.5 text-sm font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 hover:bg-white/10"
                >
                  <Phone className="h-4 w-4" /> {PHONE_DISPLAY}
                </a>
              </div>
            </div>
            <div className="relative hidden lg:col-span-2 lg:block">
              <img
                src={IMAGES.tray}
                alt="A family-size tray of layered Qubani dessert topped with almonds"
                className="ml-auto aspect-[3/4] w-72 rotate-3 rounded-[2rem] object-cover shadow-2xl ring-8 ring-white/20 transition-transform duration-700 hover:rotate-0"
              />
            </div>
          </div>
        </div>
      </Reveal>
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
              A Deccan fusion bakery reimagining the royal Qubani ka Meetha as a modern layer cake. Made by hand, in
              small batches, with a lot of love.
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
              {NAV_LINKS.map((l) => (
                <li key={l.href}>
                  <a href={l.href} className="text-[#E9E1D3]/70 transition-colors hover:text-[#F7A23B]">
                    {l.label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="text-xs font-semibold uppercase tracking-[0.25em] text-[#E9B949]">Visit & order</h4>
            <ul className="mt-5 space-y-4 text-[#E9E1D3]/70">
              <li className="flex gap-3">
                <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-[#F28500]" />
                Kranti Chowk, Aurangabad (Chhatrapati Sambhajinagar), Maharashtra
              </li>
              <li className="flex gap-3">
                <Clock className="mt-0.5 h-4 w-4 shrink-0 text-[#F28500]" />
                Daily · 10:00 am – 10:00 pm
              </li>
              <li className="flex gap-3">
                <Phone className="mt-0.5 h-4 w-4 shrink-0 text-[#F28500]" />
                {PHONE_DISPLAY}
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
  return (
    <div className="qa-root min-h-screen overflow-x-hidden bg-[#FDFBF7] text-[#1F1B16] antialiased selection:bg-[#F28500] selection:text-white">
      <style>{GLOBAL_CSS}</style>
      <Navbar />
      <main>
        <Hero />
        <LayerBreakdown />
        <Story />
        <MenuSection />
        <Reviews />
        <CtaBanner />
        <FAQ />
      </main>
      <Footer />
      <FloatingWhatsApp />
    </div>
  );
}
