@props(['name' => 'icon', 'value' => '', 'placeholder' => '😊'])

<div x-data="emojiPicker(@js($value))" class="relative">

    {{-- Hidden input submitted with the form --}}
    <input type="hidden" name="{{ $name }}" :value="selected">

    {{-- Trigger button --}}
    <button type="button"
        @click="open = !open; if (open) $nextTick(() => $refs.search && $refs.search.focus())"
        class="w-full flex items-center gap-3 px-4 py-2.5 border border-gray-300 rounded-lg hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition bg-white text-left">
        <span class="text-xl leading-none w-6 text-center flex-shrink-0"
              x-text="selected || '{{ $placeholder }}'"></span>
        <span class="text-sm flex-1 truncate"
              :class="selected ? 'text-gray-700' : 'text-gray-400'"
              x-text="selected ? selected : '{{ __('app.select_emoji') }}'"></span>
        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
             :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Picker panel --}}
    <div x-show="open" x-cloak
         @click.outside="open = false"
         @keydown.escape.window="open = false"
         class="absolute left-0 right-0 z-[9999] mt-1 bg-white border border-gray-200 rounded-xl shadow-xl p-3">

        {{-- Search --}}
        <input x-ref="search" type="text" x-model="query" @input="activeCategory = 'all'"
               placeholder="Search emoji…" autocomplete="off"
               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">

        {{-- Category tabs (hidden while searching) --}}
        <div x-show="!query" class="flex gap-1 mb-3 overflow-x-auto pb-1" style="scrollbar-width:none;-webkit-overflow-scrolling:touch">
            <template x-for="cat in categories" :key="cat.key">
                <button type="button" @click="activeCategory = cat.key"
                        :class="activeCategory === cat.key
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                        class="flex-shrink-0 px-2.5 py-1 text-xs font-medium rounded-full transition"
                        x-text="cat.label">
                </button>
            </template>
        </div>

        {{-- Emoji grid --}}
        <div class="grid grid-cols-8 gap-0.5 max-h-44 overflow-y-auto">
            <template x-for="item in visible" :key="item.e">
                <button type="button" @click="pick(item.e)" :title="item.k"
                        :class="selected === item.e
                            ? 'bg-indigo-100 ring-1 ring-indigo-400'
                            : 'hover:bg-gray-100'"
                        class="w-full aspect-square text-xl flex items-center justify-center rounded-md transition"
                        x-text="item.e">
                </button>
            </template>
            <div x-show="visible.length === 0"
                 class="col-span-8 py-6 text-center text-xs text-gray-400">
                {{ __('app.no_data') }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
            <button type="button" x-show="selected" @click="pick(''); open = false"
                    class="text-xs text-red-400 hover:text-red-600 transition">
                {{ __('app.reset') }}
            </button>
            <span x-show="!selected" class="text-xs text-gray-300 select-none">—</span>
            <button type="button" @click="open = false"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                {{ __('app.confirm') }} ✓
            </button>
        </div>
    </div>
</div>

@once
<script>
function emojiPicker(initial) {
    const raw = [
        // Finance
        { e: '💰', c: 'finance', k: 'money cash finance wallet' },
        { e: '💵', c: 'finance', k: 'dollar money cash bill' },
        { e: '💴', c: 'finance', k: 'yen money japan' },
        { e: '💶', c: 'finance', k: 'euro money europe' },
        { e: '💳', c: 'finance', k: 'credit card payment bank' },
        { e: '🏦', c: 'finance', k: 'bank finance savings institution' },
        { e: '📈', c: 'finance', k: 'chart growth stock investment up' },
        { e: '📉', c: 'finance', k: 'chart decline stock down loss' },
        { e: '💹', c: 'finance', k: 'yen chart market stock' },
        { e: '🪙', c: 'finance', k: 'coin money gold currency' },
        { e: '💸', c: 'finance', k: 'money wings cash spending' },
        { e: '🏧', c: 'finance', k: 'atm cash machine bank' },
        { e: '💎', c: 'finance', k: 'diamond gem valuable luxury' },
        { e: '🤑', c: 'finance', k: 'money face rich wealth' },
        { e: '🧾', c: 'finance', k: 'receipt bill expense invoice' },
        { e: '📊', c: 'finance', k: 'bar chart data analytics report' },
        { e: '🏷️', c: 'finance', k: 'price tag label cost' },
        { e: '💼', c: 'finance', k: 'briefcase business work salary' },
        { e: '🏪', c: 'finance', k: 'store shop convenience' },
        { e: '🏬', c: 'finance', k: 'department store shopping' },
        // Food
        { e: '🍔', c: 'food', k: 'burger food fast food beef' },
        { e: '🍕', c: 'food', k: 'pizza food italian' },
        { e: '🍣', c: 'food', k: 'sushi japanese food fish' },
        { e: '🍜', c: 'food', k: 'noodles ramen soup bowl' },
        { e: '🍦', c: 'food', k: 'ice cream dessert sweet soft serve' },
        { e: '🥗', c: 'food', k: 'salad healthy green food' },
        { e: '🍺', c: 'food', k: 'beer drink alcohol pub' },
        { e: '☕', c: 'food', k: 'coffee hot drink cafe latte' },
        { e: '🍎', c: 'food', k: 'apple fruit healthy food' },
        { e: '🥑', c: 'food', k: 'avocado healthy food green' },
        { e: '🍰', c: 'food', k: 'cake dessert birthday sweet' },
        { e: '🛒', c: 'food', k: 'grocery shopping cart supermarket' },
        { e: '🥩', c: 'food', k: 'meat steak beef food' },
        { e: '🌮', c: 'food', k: 'taco mexican food' },
        { e: '🍱', c: 'food', k: 'bento box lunch meal japanese' },
        { e: '🥤', c: 'food', k: 'cup drink beverage straw' },
        { e: '🍫', c: 'food', k: 'chocolate sweet candy dessert' },
        { e: '🥐', c: 'food', k: 'croissant bread bakery breakfast' },
        { e: '🍷', c: 'food', k: 'wine drink alcohol red' },
        { e: '🍳', c: 'food', k: 'frying pan egg cooking breakfast' },
        // Transport
        { e: '🚗', c: 'transport', k: 'car vehicle transport drive auto' },
        { e: '🚌', c: 'transport', k: 'bus public transport commute' },
        { e: '🚇', c: 'transport', k: 'metro subway underground train' },
        { e: '✈️', c: 'transport', k: 'airplane flight plane travel air' },
        { e: '🚲', c: 'transport', k: 'bicycle bike cycle eco' },
        { e: '🛵', c: 'transport', k: 'scooter moped motorbike' },
        { e: '🚕', c: 'transport', k: 'taxi cab ride transport' },
        { e: '🚂', c: 'transport', k: 'train locomotive rail travel' },
        { e: '🛳️', c: 'transport', k: 'ship ferry boat sea cruise' },
        { e: '🏍️', c: 'transport', k: 'motorcycle motorbike bike' },
        { e: '⛽', c: 'transport', k: 'fuel gas petrol station' },
        { e: '🚁', c: 'transport', k: 'helicopter air transport' },
        { e: '🛺', c: 'transport', k: 'auto rickshaw tuk tuk' },
        { e: '🚤', c: 'transport', k: 'speedboat boat water' },
        { e: '🛴', c: 'transport', k: 'kick scooter micro urban' },
        // Shopping
        { e: '🛍️', c: 'shopping', k: 'shopping bag purchase retail' },
        { e: '👔', c: 'shopping', k: 'shirt clothes clothing office' },
        { e: '👟', c: 'shopping', k: 'sneakers shoes footwear sport' },
        { e: '👜', c: 'shopping', k: 'handbag purse bag fashion' },
        { e: '💄', c: 'shopping', k: 'lipstick makeup cosmetics beauty' },
        { e: '📱', c: 'shopping', k: 'phone mobile smartphone tech' },
        { e: '💻', c: 'shopping', k: 'laptop computer tech work' },
        { e: '🎮', c: 'shopping', k: 'gaming controller video games' },
        { e: '📺', c: 'shopping', k: 'tv television screen entertainment' },
        { e: '🎁', c: 'shopping', k: 'gift present wrapped box' },
        { e: '👗', c: 'shopping', k: 'dress clothing fashion style' },
        { e: '🧥', c: 'shopping', k: 'coat jacket clothing winter' },
        { e: '💍', c: 'shopping', k: 'ring jewelry engagement diamond' },
        { e: '⌚', c: 'shopping', k: 'watch wristwatch time accessory' },
        { e: '🖥️', c: 'shopping', k: 'desktop monitor computer screen' },
        { e: '🎧', c: 'shopping', k: 'headphones music audio' },
        { e: '📷', c: 'shopping', k: 'camera photo photography' },
        // Home
        { e: '🏠', c: 'home', k: 'house home building rent' },
        { e: '🏡', c: 'home', k: 'house garden home suburban' },
        { e: '🛋️', c: 'home', k: 'couch sofa furniture living room' },
        { e: '🧹', c: 'home', k: 'broom cleaning chores sweep' },
        { e: '💡', c: 'home', k: 'lightbulb electricity utility idea' },
        { e: '🔧', c: 'home', k: 'wrench repair maintenance fix' },
        { e: '🛏️', c: 'home', k: 'bed bedroom sleep furniture' },
        { e: '🚿', c: 'home', k: 'shower bathroom water utility' },
        { e: '🪴', c: 'home', k: 'plant potted garden indoor' },
        { e: '🧺', c: 'home', k: 'basket laundry chores washing' },
        { e: '🔑', c: 'home', k: 'key lock security home access' },
        { e: '🔒', c: 'home', k: 'lock security privacy home' },
        { e: '🖼️', c: 'home', k: 'picture frame art decoration' },
        { e: '🪞', c: 'home', k: 'mirror furniture decor' },
        { e: '🛁', c: 'home', k: 'bathtub bathroom bath' },
        // Health
        { e: '💊', c: 'health', k: 'pill medicine pharmacy drug' },
        { e: '🏥', c: 'health', k: 'hospital medical doctor clinic' },
        { e: '🧘', c: 'health', k: 'yoga meditation wellness mindfulness' },
        { e: '🏋️', c: 'health', k: 'gym weightlifting fitness exercise' },
        { e: '🦷', c: 'health', k: 'tooth dental dentist care' },
        { e: '👓', c: 'health', k: 'glasses eyewear vision optical' },
        { e: '💉', c: 'health', k: 'syringe injection vaccine medical' },
        { e: '🩺', c: 'health', k: 'stethoscope doctor medical exam' },
        { e: '🧴', c: 'health', k: 'lotion cream skincare personal care' },
        { e: '🩹', c: 'health', k: 'bandage adhesive first aid wound' },
        { e: '🏃', c: 'health', k: 'running jogging exercise sport fitness' },
        { e: '🚴', c: 'health', k: 'cycling biking exercise sport' },
        { e: '🥦', c: 'health', k: 'broccoli vegetable healthy food' },
        { e: '😷', c: 'health', k: 'mask medical face protection' },
        { e: '🧠', c: 'health', k: 'brain mind mental health' },
        // Fun
        { e: '🎬', c: 'fun', k: 'movie film cinema entertainment' },
        { e: '🎵', c: 'fun', k: 'music note song audio' },
        { e: '🎭', c: 'fun', k: 'theater performing arts drama' },
        { e: '📚', c: 'fun', k: 'books reading education library' },
        { e: '🎨', c: 'fun', k: 'art painting palette creative' },
        { e: '🎲', c: 'fun', k: 'dice board game chance' },
        { e: '🎯', c: 'fun', k: 'dart target precision sport' },
        { e: '🏀', c: 'fun', k: 'basketball sport ball game' },
        { e: '⚽', c: 'fun', k: 'soccer football sport ball' },
        { e: '🎸', c: 'fun', k: 'guitar music instrument rock' },
        { e: '🎪', c: 'fun', k: 'circus tent event performance' },
        { e: '🎤', c: 'fun', k: 'microphone karaoke singing performance' },
        { e: '🎻', c: 'fun', k: 'violin music instrument classical' },
        { e: '🏆', c: 'fun', k: 'trophy award winner achievement' },
        { e: '🎠', c: 'fun', k: 'carousel amusement park ride' },
        // Travel
        { e: '🌍', c: 'travel', k: 'earth globe world international' },
        { e: '🏖️', c: 'travel', k: 'beach vacation holiday sea sand' },
        { e: '🗺️', c: 'travel', k: 'map world travel navigation' },
        { e: '🏔️', c: 'travel', k: 'mountain snow peak hiking' },
        { e: '🎒', c: 'travel', k: 'backpack travel bag trip' },
        { e: '🏨', c: 'travel', k: 'hotel accommodation lodging stay' },
        { e: '🌴', c: 'travel', k: 'palm tree tropical island beach' },
        { e: '🧳', c: 'travel', k: 'luggage suitcase travel packing' },
        { e: '🗼', c: 'travel', k: 'tower landmark paris eiffel' },
        { e: '🌅', c: 'travel', k: 'sunrise sunset sky horizon scenic' },
        { e: '🏕️', c: 'travel', k: 'camping tent outdoor nature' },
        { e: '🌊', c: 'travel', k: 'wave ocean sea water surfing' },
        { e: '🏝️', c: 'travel', k: 'island tropical desert beach' },
        { e: '🗽', c: 'travel', k: 'statue liberty new york usa' },
        // Work
        { e: '📊', c: 'work', k: 'bar chart data analytics report' },
        { e: '💻', c: 'work', k: 'laptop computer work office' },
        { e: '📝', c: 'work', k: 'memo note writing task' },
        { e: '🏢', c: 'work', k: 'office building work corporate' },
        { e: '📞', c: 'work', k: 'telephone call phone business' },
        { e: '📋', c: 'work', k: 'clipboard list document task' },
        { e: '✏️', c: 'work', k: 'pencil write edit draft' },
        { e: '📌', c: 'work', k: 'pushpin pin task location' },
        { e: '🗂️', c: 'work', k: 'card index dividers folder files' },
        { e: '🖊️', c: 'work', k: 'pen writing sign' },
        { e: '🖨️', c: 'work', k: 'printer print office document' },
        { e: '📎', c: 'work', k: 'paperclip attach document file' },
        { e: '✂️', c: 'work', k: 'scissors cut stationery' },
        { e: '📁', c: 'work', k: 'folder file directory project' },
        { e: '🔭', c: 'work', k: 'telescope research science discovery' },
        // Education
        { e: '🎓', c: 'education', k: 'graduation cap degree university school' },
        { e: '📖', c: 'education', k: 'open book reading study' },
        { e: '🔬', c: 'education', k: 'microscope science research lab' },
        { e: '🧮', c: 'education', k: 'abacus math calculation' },
        { e: '📐', c: 'education', k: 'triangle ruler geometry drawing' },
        { e: '🗒️', c: 'education', k: 'spiral notepad notes study' },
        { e: '🏫', c: 'education', k: 'school building education class' },
        { e: '📏', c: 'education', k: 'ruler measure straight stationery' },
        { e: '🧪', c: 'education', k: 'test tube chemistry science experiment' },
        { e: '📓', c: 'education', k: 'notebook journal study writing' },
        { e: '🖋️', c: 'education', k: 'fountain pen ink writing calligraphy' },
        { e: '📜', c: 'education', k: 'scroll document certificate diploma' },
    ];

    return {
        open: false,
        selected: initial || '',
        query: '',
        activeCategory: 'all',
        categories: [
            { key: 'all',       label: '🌟 All'       },
            { key: 'finance',   label: '💰 Finance'   },
            { key: 'food',      label: '🍔 Food'      },
            { key: 'transport', label: '🚗 Transport' },
            { key: 'shopping',  label: '🛍️ Shopping'  },
            { key: 'home',      label: '🏠 Home'      },
            { key: 'health',    label: '💊 Health'    },
            { key: 'fun',       label: '🎬 Fun'       },
            { key: 'travel',    label: '✈️ Travel'    },
            { key: 'work',      label: '💼 Work'      },
            { key: 'education', label: '📚 Education' },
        ],
        get visible() {
            const q = this.query.toLowerCase().trim();
            if (q) {
                return raw.filter(item =>
                    item.e.includes(q) || item.k.includes(q)
                );
            }
            if (this.activeCategory === 'all') return raw;
            return raw.filter(item => item.c === this.activeCategory);
        },
        pick(emoji) {
            this.selected = emoji;
            this.open = false;
            this.query = '';
        },
    };
}
</script>
@endonce
