<!--
URL: /guides/quick-commerce-commissions-and-margins-india/
PAGE TYPE: Cluster guide
TITLE TAG: Quick Commerce Commissions and Margins in India
META DESCRIPTION: How quick-commerce fees work on Blinkit, Zepto and Instamart, how to find your real rates, and a worked ₹ margin model you can reuse for every SKU.
H1: Quick Commerce Commissions and Margins in India: How to Do the Maths
SOURCES CHECKED: 15 November 2026
MARKERS: [VERIFY] = not verifiable from a public primary source; the source to check is named beside it. All ₹ rates in worked examples are ILLUSTRATIVE ASSUMPTIONS.
-->

# Quick Commerce Commissions and Margins in India: How to Do the Maths

Search for "Blinkit commission" and you will find ranges from 2% to 30%, each stated with confidence and none sourced to the platform. That is because none of the three major quick-commerce apps publishes a public commission rate card. Blinkit publishes its fee structure and shows the actual amounts inside Seller Hub; Zepto and Swiggy Instamart agree terms brand by brand.

So this guide does not give you a commission percentage. It gives you something more useful: every cost line you need to account for, where to find the real figure for each, and a margin model you can reuse for every SKU. All rupee figures in the examples are labelled assumptions.

It is part of our [quick-commerce guide for Mumbai brands](/quick-commerce-agency-mumbai/).

## Why published commission figures are unreliable

Three reasons.

1. **Commission varies by SKU.** Blinkit states category commission "varies based on the product's business category and pricing" ([Blinkit fees and commissions](https://seller.blinkit.com/fees-commission)). A single percentage for a platform is therefore wrong for most products on it.
2. **Commission is only one line.** A low commission paired with a high fulfilment fee can cost more than the reverse.
3. **Negotiated terms are confidential.** Zepto and Instamart terms sit in individual agreements. A figure on a blog is, at best, one brand's deal at one point in time.

## The cost lines, platform by platform

### Blinkit (published structure)

From Blinkit's Seller Hub, checked 15 November 2026:

| Charge | Trigger (Blinkit's description) | Where to get the amount |
|---|---|---|
| Product ID activation fee | Activating a product; credited to ads wallet | Seller Hub [VERIFY] |
| Inwarding fee | Per unit received at a Blinkit warehouse | Seller Hub [VERIFY] |
| Storage fee | Daily, per unsold unit in warehouses or dark stores | Seller Hub [VERIFY] |
| Category commission | Every sale; by category and price | Commissions calculator [VERIFY] |
| Fulfilment fee | Each delivered customer order | Seller Hub [VERIFY] |
| Customer return fee | Returns attributed to seller | Seller Hub [VERIFY] |
| Inventory removal fee | Per unit, on seller request | Seller Hub [VERIFY] |

Blinkit's page directs sellers to "log in to Seller Hub to view product-specific fees and use the commissions calculator". Do this for every SKU at your intended selling price before you activate it.

### Zepto and Swiggy Instamart (not published)

Neither [Zepto's Vendor Hub](https://brands.zepto.co.in/) nor [Instamart's Brand Portal](https://partner.instamart.in/) publishes a fee structure. Ask for these items in writing, and do not sign until each has a figure or a formula:

- commission or margin, and the price it is calculated on;
- logistics, fulfilment or handling charges;
- any charges for storage, inwarding, listing or onboarding;
- marketing commitments or mandatory ad spend;
- penalties for short supply, late delivery to warehouse, or quality rejections;
- return, damage and expiry handling, and who bears the cost;
- payment terms in days;
- whether the platform or you set the retail price.

## Taxes that affect your payout

These are not platform fees, but they change cash in hand. Confirm every rate and section number with your chartered accountant; the figures change through Budget and GST Council decisions.

- **GST on platform fees.** Platforms charge GST on their service fees. A GST-registered seller normally claims this as input tax credit. [VERIFY the rate on your platform invoices.]
- **TCS under GST.** E-commerce operators collect tax at source on net taxable supplies made through them and deposit it; the seller claims credit for it in their GST electronic cash ledger. [VERIFY the current rate on [cbic-gst.gov.in](https://cbic-gst.gov.in/) and confirm it appears in your GSTR-2X / TCS credit on the [GST portal](https://www.gst.gov.in/).]
- **TDS under the Income-tax Act.** E-commerce operators deduct TDS on payments to e-commerce participants. [VERIFY the current section and rate with your accountant.]
- **GST on your sale.** Output GST on the selling price is yours to pay. Your contribution should always be calculated on the ex-GST price.

TCS and TDS are recoverable, but they reduce your payout until you claim them. For a brand with tight working capital, that timing matters.

## The margin model

Use this structure for every SKU on every platform. Work in rupees per unit, ex-GST.

```
Net revenue      = Selling price ÷ (1 + GST rate)
Platform fees    = Commission + Fulfilment + Inwarding + (Storage × days held)
                   + (Return fee × return rate) + Activation fee ÷ expected units
Your other costs = Landed COGS + Freight to platform warehouse + Packaging
                   + Ad spend per unit + Damage/expiry provision
Contribution     = Net revenue − Platform fees − Your other costs
```

### Worked example 1: a ₹199 snack pack

**Every rate here is an illustrative assumption, not a platform rate.**

| Line | Assumption | ₹ |
|---|---|---|
| Selling price (incl. GST) | Set by brand | 199.00 |
| GST rate | Assumed 12% | |
| **Net revenue** | 199 ÷ 1.12 | **177.68** |
| Landed COGS | Assumed | 62.00 |
| Commission | Assumed 18% of net revenue | 31.98 |
| Fulfilment fee | Assumed flat | 18.00 |
| Inwarding fee | Assumed | 3.00 |
| Storage | Assumed ₹0.80/day × 14 days | 11.20 |
| Freight to warehouse | Assumed | 4.00 |
| Ads | Assumed 12% of net revenue | 21.32 |
| Damage/expiry provision | Assumed 3% of net revenue | 5.33 |
| **Contribution** | | **20.85 (11.7%)** |

### Worked example 2: the same product at ₹99

Halve the pack and price, keep the same assumed rates.

| Line | ₹ |
|---|---|
| Net revenue (99 ÷ 1.12) | 88.39 |
| Landed COGS | 33.00 |
| Commission (18%) | 15.91 |
| Fulfilment (flat) | 18.00 |
| Inwarding | 3.00 |
| Storage (14 days) | 11.20 |
| Freight | 2.50 |
| Ads (12%) | 10.61 |
| Provision (3%) | 2.65 |
| **Contribution** | **−8.48 (loss)** |

The percentage fees scale down with price; the flat fees do not. This is the most important pattern in quick-commerce economics, and it holds whatever the real rates turn out to be.

### Break-even price

Rearranging the model, the minimum net revenue that covers costs is:

```
Break-even net revenue = (Fixed per-unit costs) ÷ (1 − Percentage costs)
```

With example 1's assumptions, fixed per-unit costs (COGS 62 + fulfilment 18 + inwarding 3 + storage 11.20 + freight 4 = 98.20) and percentage costs (18% + 12% + 3% = 33%) give a break-even net revenue of 98.20 ÷ 0.67 = ₹146.57, or ₹164.16 including 12% GST. Below that selling price, every unit loses money.

## The levers that actually move margin

1. **Days held.** Storage is charged daily on unsold stock. Sending less, more often, can be cheaper than one large shipment. Use the platform's replenishment recommendations as a ceiling, not a target.
2. **Pack and price architecture.** Bundles and larger packs spread the flat fulfilment fee.
3. **Return attribution.** Blinkit charges a return fee only where the reason is attributed to the seller. Leaking caps and short-dated stock create seller-attributed returns. Packaging is a margin lever; see our [packaging requirements guide](/guides/quick-commerce-packaging-requirements/).
4. **Ad efficiency.** Ads on out-of-stock stores are wasted. Fix availability before raising budgets.
5. **Selling price.** Blinkit lets you change price at will. Test small increases; quick-commerce buyers are often less price-sensitive than marketplace buyers on small baskets. [VERIFY for your category by testing.]

## How to check your real numbers

1. **Blinkit:** log in to Seller Hub, open the commissions calculator, enter each SKU at its intended price, and record every fee. Re-check after any price change, since commission depends on price.
2. **Zepto and Instamart:** ask for the term sheet and a sample settlement report. A settlement report shows exactly what is deducted and when.
3. **After the first payout:** reconcile payout against sales, line by line. Payouts on Blinkit arrive net of fees on the third working day after each fortnightly cycle ([Blinkit FAQ](https://seller.blinkit.com/faq)).
4. **Every quarter:** re-run the model. Fees, ad costs and sell-through all drift.

## Comparing platforms fairly

| Compare | Don't compare |
|---|---|
| Contribution per unit at the same selling price | Headline commission percentage |
| Contribution per month (units × contribution) | Platform-wide order volume claims |
| Cash conversion: days from shipment to payout | Registration fee alone |
| Seller-attributed return rate | Fulfilment fee alone |

## Frequently asked questions

### What is Blinkit's commission?

Blinkit does not publish a single rate. It states that commission varies by business category and price, and shows the exact figure per product in the Seller Hub commissions calculator.

### Is commission charged on MRP or selling price?

Blinkit says it varies with "pricing" but does not state the base publicly. [VERIFY in the commissions calculator or your agreement: MRP or selling price, and GST-inclusive or exclusive.]

### Are there hidden fees?

Blinkit publishes all seven charge types on its fees page. For Zepto and Instamart, the full list is in your agreement; insist on seeing it before signing.

### What margin should I aim for on quick commerce?

There is no universal figure. Set a minimum contribution per unit that covers your overheads and target profit, and do not list SKUs that fall below it after real fees.

### Why is my payout lower than sales minus commission?

Most likely: storage, inwarding, fulfilment, return fees, ad wallet deductions, GST on fees, and TCS and TDS. Check your settlement report line by line.

### Can you build this model for my catalogue?

Yes. Send us your SKUs, prices and landed costs. Our [quick-commerce onboarding service](/blinkit-seller-onboarding/) includes a per-SKU margin model using your actual dashboard rates.

## Related guides

- [Blinkit seller onboarding checklist](/guides/blinkit-seller-onboarding-checklist/)
- [Dark store vs own warehouse](/guides/dark-store-vs-own-warehouse/)
- Back to the [quick-commerce pillar guide](/quick-commerce-agency-mumbai/)
