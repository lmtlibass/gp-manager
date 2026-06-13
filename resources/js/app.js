/**
 * GP Manager — Alpine.js components
 * Country picker + Phone picker with flag & dial code
 */

const GP_COUNTRIES = [
    // ── Afrique de l'Ouest ──────────────────────────────
    { code: 'SN', name: 'Sénégal',         dial: '+221', flag: '🇸🇳' },
    { code: 'ML', name: 'Mali',             dial: '+223', flag: '🇲🇱' },
    { code: 'GN', name: 'Guinée',           dial: '+224', flag: '🇬🇳' },
    { code: 'CI', name: "Côte d'Ivoire",    dial: '+225', flag: '🇨🇮' },
    { code: 'BF', name: 'Burkina Faso',     dial: '+226', flag: '🇧🇫' },
    { code: 'NE', name: 'Niger',            dial: '+227', flag: '🇳🇪' },
    { code: 'TG', name: 'Togo',             dial: '+228', flag: '🇹🇬' },
    { code: 'BJ', name: 'Bénin',            dial: '+229', flag: '🇧🇯' },
    { code: 'MR', name: 'Mauritanie',       dial: '+222', flag: '🇲🇷' },
    { code: 'GM', name: 'Gambie',           dial: '+220', flag: '🇬🇲' },
    { code: 'GW', name: 'Guinée-Bissau',    dial: '+245', flag: '🇬🇼' },
    { code: 'CV', name: 'Cap-Vert',         dial: '+238', flag: '🇨🇻' },
    { code: 'SL', name: 'Sierra Leone',     dial: '+232', flag: '🇸🇱' },
    { code: 'LR', name: 'Libéria',          dial: '+231', flag: '🇱🇷' },
    { code: 'GH', name: 'Ghana',            dial: '+233', flag: '🇬🇭' },
    { code: 'NG', name: 'Nigeria',          dial: '+234', flag: '🇳🇬' },
    // ── Afrique Centrale ─────────────────────────────────
    { code: 'CM', name: 'Cameroun',         dial: '+237', flag: '🇨🇲' },
    { code: 'GA', name: 'Gabon',            dial: '+241', flag: '🇬🇦' },
    { code: 'CG', name: 'Congo',            dial: '+242', flag: '🇨🇬' },
    { code: 'CD', name: 'RD Congo',         dial: '+243', flag: '🇨🇩' },
    { code: 'GQ', name: 'Guinée Équat.',    dial: '+240', flag: '🇬🇶' },
    // ── Afrique du Nord ──────────────────────────────────
    { code: 'MA', name: 'Maroc',            dial: '+212', flag: '🇲🇦' },
    { code: 'DZ', name: 'Algérie',          dial: '+213', flag: '🇩🇿' },
    { code: 'TN', name: 'Tunisie',          dial: '+216', flag: '🇹🇳' },
    // ── Europe ───────────────────────────────────────────
    { code: 'FR', name: 'France',           dial: '+33',  flag: '🇫🇷' },
    { code: 'BE', name: 'Belgique',         dial: '+32',  flag: '🇧🇪' },
    { code: 'CH', name: 'Suisse',           dial: '+41',  flag: '🇨🇭' },
    { code: 'IT', name: 'Italie',           dial: '+39',  flag: '🇮🇹' },
    { code: 'ES', name: 'Espagne',          dial: '+34',  flag: '🇪🇸' },
    { code: 'PT', name: 'Portugal',         dial: '+351', flag: '🇵🇹' },
    { code: 'GB', name: 'Royaume-Uni',      dial: '+44',  flag: '🇬🇧' },
    { code: 'DE', name: 'Allemagne',        dial: '+49',  flag: '🇩🇪' },
    { code: 'NL', name: 'Pays-Bas',         dial: '+31',  flag: '🇳🇱' },
    { code: 'LU', name: 'Luxembourg',       dial: '+352', flag: '🇱🇺' },
    // ── Amériques ────────────────────────────────────────
    { code: 'US', name: 'États-Unis',       dial: '+1',   flag: '🇺🇸' },
    { code: 'CA', name: 'Canada',           dial: '+1',   flag: '🇨🇦' },
];

document.addEventListener('alpine:init', () => {

    /**
     * Country picker — exposes `value` (country name string) via x-modelable
     * Usage: <div x-data="countryPicker()" x-modelable="value" wire:model="origin_country">
     */
    Alpine.data('countryPicker', () => ({
        open:     false,
        search:   '',
        value:    '',
        selected: null,

        get filtered() {
            const q = this.search.toLowerCase().trim();
            return q
                ? GP_COUNTRIES.filter(c =>
                    c.name.toLowerCase().includes(q) ||
                    c.code.toLowerCase().includes(q) ||
                    c.dial.includes(q))
                : GP_COUNTRIES;
        },

        init() {
            // Re-sync display when Livewire updates value (e.g. edit form)
            this.$watch('value', v => {
                this.selected = GP_COUNTRIES.find(c => c.name === v) ?? null;
            });
        },

        select(c) {
            this.selected = c;
            this.value    = c.name;
            this.open     = false;
            this.search   = '';
        },

        clear(e) {
            e.stopPropagation();
            this.selected = null;
            this.value    = '';
        },
    }));

    /**
     * Phone picker — exposes `value` (full E.164-ish string) via x-modelable
     * Usage: <div x-data="phonePicker()" x-modelable="value" wire:model="phone">
     */
    Alpine.data('phonePicker', () => ({
        open:        false,
        search:      '',
        value:       '',
        localNumber: '',
        selected:    GP_COUNTRIES.find(c => c.code === 'FR'),

        get filtered() {
            const q = this.search.toLowerCase().trim();
            return q
                ? GP_COUNTRIES.filter(c =>
                    c.name.toLowerCase().includes(q) ||
                    c.dial.includes(q))
                : GP_COUNTRIES;
        },

        init() {
            this.$watch('value', v => this._parseValue(v));
        },

        _parseValue(v) {
            if (!v) { this.localNumber = ''; return; }
            // Match longest dial code first to avoid +1 matching +1-xxx
            const match = [...GP_COUNTRIES]
                .sort((a, b) => b.dial.length - a.dial.length)
                .find(c => v.startsWith(c.dial));
            if (match) {
                this.selected    = match;
                this.localNumber = v.slice(match.dial.length).trim();
            } else {
                this.localNumber = v;
            }
        },

        selectDialCode(c) {
            this.selected = c;
            this.open     = false;
            this.search   = '';
            this._sync();
        },

        _sync() {
            const n = this.localNumber.trim().replace(/^0+/, '');
            this.value = n ? `${this.selected.dial}${n}` : '';
        },
    }));
});
