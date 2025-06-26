<div>

    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-8">
        <h1 class="mb-6 text-2xl">Personal Information</h1>
        <p class="text-sm text-gray-500 mb-4">* Indicates required field</p>
        <form id="infoForm" wire:submit.prevent="proceedToPayment">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.forms.group label="First name *" for="first_name" error="first_name">
                    <x-ui.forms.input
                        name="first_name"
                        id="first_name"
                        wire:model.blur="first_name"
                        autocomplete="given-name"
                        required
                    />
                </x-ui.forms.group>

                <x-ui.forms.group label="Last name *" for="last_name" error="last_name">
                    <x-ui.forms.input
                        name="last_name"
                        id="last_name"
                        wire:model.blur="last_name"
                        autocomplete="family-name"
                        required
                    />
                </x-ui.forms.group>
            </div>

            <x-ui.forms.group label="Email *" for="email" error="email" class="mb-4">
                <x-ui.forms.input
                    type="email"
                    name="email"
                    id="email"
                    wire:model.blur="email"
                    autocomplete="email"
                    required
                />
            </x-ui.forms.group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.forms.group label="Phone number *" for="phone" error="phone">
                    <x-ui.forms.input
                        type="tel"
                        name="phone"
                        id="phone"
                        wire:model.blur="phone"
                        autocomplete="tel"
                        required
                    />
                </x-ui.forms.group>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.forms.group label="Date of birth" for="date_of_birth" error="date_of_birth" class="mb-4">
                    <x-ui.forms.input
                        type="date"
                        name="date_of_birth"
                        id="date_of_birth"
                        wire:model.blur="date_of_birth"
                        autocomplete="bday"
                    />
                </x-ui.forms.group>

                <x-ui.forms.group label="Gender" for="gender" class="mb-4" error="gender">
                    <x-ui.forms.select name="gender" id="gender" wire:model.blur="gender" class="w-full">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer not to say">Prefer not to say</option>
                    </x-ui.forms.select>
                </x-ui.forms.group>
            </div>

            <x-ui.forms.group label="Address" for="address" error="address" class="mb-4">
                <x-ui.forms.input
                    name="address"
                    id="address"
                    wire:model.blur="address"
                    autocomplete="address-line1"
                />
            </x-ui.forms.group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.forms.group label="City" for="city" error="city">
                    <x-ui.forms.input
                        name="city"
                        id="city"
                        wire:model.blur="city"
                        autocomplete="address-level2"
                    />
                </x-ui.forms.group>

                <x-ui.forms.group label="Country" for="country" error="country">
                    <x-ui.forms.select name="country" id="country" wire:model.blur="country" class="w-full">
                        <option value="">Select an option</option>
                        <option value="1: AF" class="ng-star-inserted"> Afghanistan <!----><!----><!----></option><option value="2: AX" class="ng-star-inserted"> Åland Islands <!----><!----><!----></option><option value="3: AL" class="ng-star-inserted"> Albania <!----><!----><!----></option><option value="4: DZ" class="ng-star-inserted"> Algeria <!----><!----><!----></option><option value="5: AS" class="ng-star-inserted"> American Samoa <!----><!----><!----></option><option value="6: AD" class="ng-star-inserted"> Andorra <!----><!----><!----></option><option value="7: AO" class="ng-star-inserted"> Angola <!----><!----><!----></option><option value="8: AI" class="ng-star-inserted"> Anguilla <!----><!----><!----></option><option value="9: AQ" class="ng-star-inserted"> Antarctica <!----><!----><!----></option><option value="10: AG" class="ng-star-inserted"> Antigua and Barbuda <!----><!----><!----></option><option value="11: AR" class="ng-star-inserted"> Argentina <!----><!----><!----></option><option value="12: AM" class="ng-star-inserted"> Armenia <!----><!----><!----></option><option value="13: AW" class="ng-star-inserted"> Aruba <!----><!----><!----></option><option value="14: AU" class="ng-star-inserted"> Australia <!----><!----><!----></option><option value="15: AT" class="ng-star-inserted"> Austria <!----><!----><!----></option><option value="16: AZ" class="ng-star-inserted"> Azerbaijan <!----><!----><!----></option><option value="17: BS" class="ng-star-inserted"> Bahamas <!----><!----><!----></option><option value="18: BH" class="ng-star-inserted"> Bahrain <!----><!----><!----></option><option value="19: BD" class="ng-star-inserted"> Bangladesh <!----><!----><!----></option><option value="20: BB" class="ng-star-inserted"> Barbados <!----><!----><!----></option><option value="21: BY" class="ng-star-inserted"> Belarus <!----><!----><!----></option><option value="22: BE" class="ng-star-inserted"> Belgium <!----><!----><!----></option><option value="23: BZ" class="ng-star-inserted"> Belize <!----><!----><!----></option><option value="24: BJ" class="ng-star-inserted"> Benin <!----><!----><!----></option><option value="25: BM" class="ng-star-inserted"> Bermuda <!----><!----><!----></option><option value="26: BT" class="ng-star-inserted"> Bhutan <!----><!----><!----></option><option value="27: BO" class="ng-star-inserted"> Bolivia (Plurinational State of) <!----><!----><!----></option><option value="28: BQ" class="ng-star-inserted"> Bonaire, Sint Eustatius and Saba <!----><!----><!----></option><option value="29: BA" class="ng-star-inserted"> Bosnia and Herzegovina <!----><!----><!----></option><option value="30: BW" class="ng-star-inserted"> Botswana <!----><!----><!----></option><option value="31: BV" class="ng-star-inserted"> Bouvet Island <!----><!----><!----></option><option value="32: BR" class="ng-star-inserted"> Brazil <!----><!----><!----></option><option value="33: IO" class="ng-star-inserted"> British Indian Ocean Territory <!----><!----><!----></option><option value="34: BN" class="ng-star-inserted"> Brunei Darussalam <!----><!----><!----></option><option value="35: BG" class="ng-star-inserted"> Bulgaria <!----><!----><!----></option><option value="36: BF" class="ng-star-inserted"> Burkina Faso <!----><!----><!----></option><option value="37: BI" class="ng-star-inserted"> Burundi <!----><!----><!----></option><option value="38: CV" class="ng-star-inserted"> Cabo Verde <!----><!----><!----></option><option value="39: KH" class="ng-star-inserted"> Cambodia <!----><!----><!----></option><option value="40: CM" class="ng-star-inserted"> Cameroon <!----><!----><!----></option><option value="41: CA" class="ng-star-inserted"> Canada <!----><!----><!----></option><option value="42: KY" class="ng-star-inserted"> Cayman Islands <!----><!----><!----></option><option value="43: CF" class="ng-star-inserted"> Central African Republic <!----><!----><!----></option><option value="44: TD" class="ng-star-inserted"> Chad <!----><!----><!----></option><option value="45: CL" class="ng-star-inserted"> Chile <!----><!----><!----></option><option value="46: CN" class="ng-star-inserted"> China <!----><!----><!----></option><option value="47: CX" class="ng-star-inserted"> Christmas Island <!----><!----><!----></option><option value="48: CC" class="ng-star-inserted"> Cocos (Keeling) Islands <!----><!----><!----></option><option value="49: CO" class="ng-star-inserted"> Colombia <!----><!----><!----></option><option value="50: KM" class="ng-star-inserted"> Comoros <!----><!----><!----></option><option value="51: CG" class="ng-star-inserted"> Congo <!----><!----><!----></option><option value="52: CD" class="ng-star-inserted"> Congo (the Democratic Republic of the) <!----><!----><!----></option><option value="53: CK" class="ng-star-inserted"> Cook Islands <!----><!----><!----></option><option value="54: CR" class="ng-star-inserted"> Costa Rica <!----><!----><!----></option><option value="55: CI" class="ng-star-inserted"> Côte d'Ivoire <!----><!----><!----></option><option value="56: HR" class="ng-star-inserted"> Croatia <!----><!----><!----></option><option value="57: CU" class="ng-star-inserted"> Cuba <!----><!----><!----></option><option value="58: CW" class="ng-star-inserted"> Curaçao <!----><!----><!----></option><option value="59: CY" class="ng-star-inserted"> Cyprus <!----><!----><!----></option><option value="60: CZ" class="ng-star-inserted"> Czechia <!----><!----><!----></option><option value="61: DK" class="ng-star-inserted"> Denmark <!----><!----><!----></option><option value="62: DJ" class="ng-star-inserted"> Djibouti <!----><!----><!----></option><option value="63: DM" class="ng-star-inserted"> Dominica <!----><!----><!----></option><option value="64: DO" class="ng-star-inserted"> Dominican Republic <!----><!----><!----></option><option value="65: EC" class="ng-star-inserted"> Ecuador <!----><!----><!----></option><option value="66: EG" class="ng-star-inserted"> Egypt <!----><!----><!----></option><option value="67: SV" class="ng-star-inserted"> El Salvador <!----><!----><!----></option><option value="68: GQ" class="ng-star-inserted"> Equatorial Guinea <!----><!----><!----></option><option value="69: ER" class="ng-star-inserted"> Eritrea <!----><!----><!----></option><option value="70: EE" class="ng-star-inserted"> Estonia <!----><!----><!----></option><option value="71: SZ" class="ng-star-inserted"> Eswatini <!----><!----><!----></option><option value="72: ET" class="ng-star-inserted"> Ethiopia <!----><!----><!----></option><option value="73: FK" class="ng-star-inserted"> Falkland Islands (Malvinas) <!----><!----><!----></option><option value="74: FO" class="ng-star-inserted"> Faroe Islands <!----><!----><!----></option><option value="75: FJ" class="ng-star-inserted"> Fiji <!----><!----><!----></option><option value="76: FI" class="ng-star-inserted"> Finland <!----><!----><!----></option><option value="77: FR" class="ng-star-inserted"> France <!----><!----><!----></option><option value="78: GF" class="ng-star-inserted"> French Guiana <!----><!----><!----></option><option value="79: PF" class="ng-star-inserted"> French Polynesia <!----><!----><!----></option><option value="80: TF" class="ng-star-inserted"> French Southern Territories <!----><!----><!----></option><option value="81: GA" class="ng-star-inserted"> Gabon <!----><!----><!----></option><option value="82: GM" class="ng-star-inserted"> Gambia <!----><!----><!----></option><option value="83: GE" class="ng-star-inserted"> Georgia <!----><!----><!----></option><option value="84: DE" class="ng-star-inserted"> Germany <!----><!----><!----></option><option value="85: GH" class="ng-star-inserted"> Ghana <!----><!----><!----></option><option value="86: GI" class="ng-star-inserted"> Gibraltar <!----><!----><!----></option><option value="87: GR" class="ng-star-inserted"> Greece <!----><!----><!----></option><option value="88: GL" class="ng-star-inserted"> Greenland <!----><!----><!----></option><option value="89: GD" class="ng-star-inserted"> Grenada <!----><!----><!----></option><option value="90: GP" class="ng-star-inserted"> Guadeloupe <!----><!----><!----></option><option value="91: GU" class="ng-star-inserted"> Guam <!----><!----><!----></option><option value="92: GT" class="ng-star-inserted"> Guatemala <!----><!----><!----></option><option value="93: GG" class="ng-star-inserted"> Guernsey <!----><!----><!----></option><option value="94: GN" class="ng-star-inserted"> Guinea <!----><!----><!----></option><option value="95: GW" class="ng-star-inserted"> Guinea-Bissau <!----><!----><!----></option><option value="96: GY" class="ng-star-inserted"> Guyana <!----><!----><!----></option><option value="97: HT" class="ng-star-inserted"> Haiti <!----><!----><!----></option><option value="98: HM" class="ng-star-inserted"> Heard Island and McDonald Islands <!----><!----><!----></option><option value="99: VA" class="ng-star-inserted"> Holy See <!----><!----><!----></option><option value="100: HN" class="ng-star-inserted"> Honduras <!----><!----><!----></option><option value="101: HK" class="ng-star-inserted"> Hong Kong <!----><!----><!----></option><option value="102: HU" class="ng-star-inserted"> Hungary <!----><!----><!----></option><option value="103: IS" class="ng-star-inserted"> Iceland <!----><!----><!----></option><option value="104: IN" class="ng-star-inserted"> India <!----><!----><!----></option><option value="105: ID" class="ng-star-inserted"> Indonesia <!----><!----><!----></option><option value="106: IR" class="ng-star-inserted"> Iran (Islamic Republic of) <!----><!----><!----></option><option value="107: IQ" class="ng-star-inserted"> Iraq <!----><!----><!----></option><option value="108: IE" class="ng-star-inserted"> Ireland <!----><!----><!----></option><option value="109: IM" class="ng-star-inserted"> Isle of Man <!----><!----><!----></option><option value="110: IL" class="ng-star-inserted"> Israel <!----><!----><!----></option><option value="111: IT" class="ng-star-inserted"> Italy <!----><!----><!----></option><option value="112: JM" class="ng-star-inserted"> Jamaica <!----><!----><!----></option><option value="113: JP" class="ng-star-inserted"> Japan <!----><!----><!----></option><option value="114: JE" class="ng-star-inserted"> Jersey <!----><!----><!----></option><option value="115: JO" class="ng-star-inserted"> Jordan <!----><!----><!----></option><option value="116: KZ" class="ng-star-inserted"> Kazakhstan <!----><!----><!----></option><option value="117: KE" class="ng-star-inserted"> Kenya <!----><!----><!----></option><option value="118: KI" class="ng-star-inserted"> Kiribati <!----><!----><!----></option><option value="119: KP" class="ng-star-inserted"> Korea (the Democratic People's Republic of) <!----><!----><!----></option><option value="120: KR" class="ng-star-inserted"> Korea (the Republic of) <!----><!----><!----></option><option value="121: KW" class="ng-star-inserted"> Kuwait <!----><!----><!----></option><option value="122: KG" class="ng-star-inserted"> Kyrgyzstan <!----><!----><!----></option><option value="123: LA" class="ng-star-inserted"> Lao People's Democratic Republic <!----><!----><!----></option><option value="124: LV" class="ng-star-inserted"> Latvia <!----><!----><!----></option><option value="125: LB" class="ng-star-inserted"> Lebanon <!----><!----><!----></option><option value="126: LS" class="ng-star-inserted"> Lesotho <!----><!----><!----></option><option value="127: LR" class="ng-star-inserted"> Liberia <!----><!----><!----></option><option value="128: LY" class="ng-star-inserted"> Libya <!----><!----><!----></option><option value="129: LI" class="ng-star-inserted"> Liechtenstein <!----><!----><!----></option><option value="130: LT" class="ng-star-inserted"> Lithuania <!----><!----><!----></option><option value="131: LU" class="ng-star-inserted"> Luxembourg <!----><!----><!----></option><option value="132: MO" class="ng-star-inserted"> Macao <!----><!----><!----></option><option value="133: MG" class="ng-star-inserted"> Madagascar <!----><!----><!----></option><option value="134: MW" class="ng-star-inserted"> Malawi <!----><!----><!----></option><option value="135: MY" class="ng-star-inserted"> Malaysia <!----><!----><!----></option><option value="136: MV" class="ng-star-inserted"> Maldives <!----><!----><!----></option><option value="137: ML" class="ng-star-inserted"> Mali <!----><!----><!----></option><option value="138: MT" class="ng-star-inserted"> Malta <!----><!----><!----></option><option value="139: MH" class="ng-star-inserted"> Marshall Islands <!----><!----><!----></option><option value="140: MQ" class="ng-star-inserted"> Martinique <!----><!----><!----></option><option value="141: MR" class="ng-star-inserted"> Mauritania <!----><!----><!----></option><option value="142: MU" class="ng-star-inserted"> Mauritius <!----><!----><!----></option><option value="143: YT" class="ng-star-inserted"> Mayotte <!----><!----><!----></option><option value="144: MX" class="ng-star-inserted"> Mexico <!----><!----><!----></option><option value="145: FM" class="ng-star-inserted"> Micronesia (Federated States of) <!----><!----><!----></option><option value="146: MD" class="ng-star-inserted"> Moldova (the Republic of) <!----><!----><!----></option><option value="147: MC" class="ng-star-inserted"> Monaco <!----><!----><!----></option><option value="148: MN" class="ng-star-inserted"> Mongolia <!----><!----><!----></option><option value="149: ME" class="ng-star-inserted"> Montenegro <!----><!----><!----></option><option value="150: MS" class="ng-star-inserted"> Montserrat <!----><!----><!----></option><option value="151: MA" class="ng-star-inserted"> Morocco <!----><!----><!----></option><option value="152: MZ" class="ng-star-inserted"> Mozambique <!----><!----><!----></option><option value="153: MM" class="ng-star-inserted"> Myanmar <!----><!----><!----></option><option value="154: NA" class="ng-star-inserted"> Namibia <!----><!----><!----></option><option value="155: NR" class="ng-star-inserted"> Nauru <!----><!----><!----></option><option value="156: NP" class="ng-star-inserted"> Nepal <!----><!----><!----></option><option value="157: NL" class="ng-star-inserted"> Netherlands <!----><!----><!----></option><option value="158: NC" class="ng-star-inserted"> New Caledonia <!----><!----><!----></option><option value="159: NZ" class="ng-star-inserted"> New Zealand <!----><!----><!----></option><option value="160: NI" class="ng-star-inserted"> Nicaragua <!----><!----><!----></option><option value="161: NE" class="ng-star-inserted"> Niger <!----><!----><!----></option><option value="162: NG" class="ng-star-inserted"> Nigeria <!----><!----><!----></option><option value="163: NU" class="ng-star-inserted"> Niue <!----><!----><!----></option><option value="164: NF" class="ng-star-inserted"> Norfolk Island <!----><!----><!----></option><option value="165: MK" class="ng-star-inserted"> North Macedonia <!----><!----><!----></option><option value="166: MP" class="ng-star-inserted"> Northern Mariana Islands <!----><!----><!----></option><option value="167: NO" class="ng-star-inserted"> Norway <!----><!----><!----></option><option value="168: OM" class="ng-star-inserted"> Oman <!----><!----><!----></option><option value="169: PK" class="ng-star-inserted"> Pakistan <!----><!----><!----></option><option value="170: PW" class="ng-star-inserted"> Palau <!----><!----><!----></option><option value="171: PS" class="ng-star-inserted"> Palestine, State of <!----><!----><!----></option><option value="172: PA" class="ng-star-inserted"> Panama <!----><!----><!----></option><option value="173: PG" class="ng-star-inserted"> Papua New Guinea <!----><!----><!----></option><option value="174: PY" class="ng-star-inserted"> Paraguay <!----><!----><!----></option><option value="175: PE" class="ng-star-inserted"> Peru <!----><!----><!----></option><option value="176: PH" class="ng-star-inserted"> Philippines <!----><!----><!----></option><option value="177: PN" class="ng-star-inserted"> Pitcairn <!----><!----><!----></option><option value="178: PL" class="ng-star-inserted"> Poland <!----><!----><!----></option><option value="179: PT" class="ng-star-inserted"> Portugal <!----><!----><!----></option><option value="180: PR" class="ng-star-inserted"> Puerto Rico <!----><!----><!----></option><option value="181: QA" class="ng-star-inserted"> Qatar <!----><!----><!----></option><option value="182: RE" class="ng-star-inserted"> Réunion <!----><!----><!----></option><option value="183: RO" class="ng-star-inserted"> Romania <!----><!----><!----></option><option value="184: RU" class="ng-star-inserted"> Russian Federation <!----><!----><!----></option><option value="185: RW" class="ng-star-inserted"> Rwanda <!----><!----><!----></option><option value="186: BL" class="ng-star-inserted"> Saint Barthélemy <!----><!----><!----></option><option value="187: SH" class="ng-star-inserted"> Saint Helena, Ascension and Tristan da Cunha <!----><!----><!----></option><option value="188: KN" class="ng-star-inserted"> Saint Kitts and Nevis <!----><!----><!----></option><option value="189: LC" class="ng-star-inserted"> Saint Lucia <!----><!----><!----></option><option value="190: MF" class="ng-star-inserted"> Saint Martin (French part) <!----><!----><!----></option><option value="191: PM" class="ng-star-inserted"> Saint Pierre and Miquelon <!----><!----><!----></option><option value="192: VC" class="ng-star-inserted"> Saint Vincent and the Grenadines <!----><!----><!----></option><option value="193: WS" class="ng-star-inserted"> Samoa <!----><!----><!----></option><option value="194: SM" class="ng-star-inserted"> San Marino <!----><!----><!----></option><option value="195: ST" class="ng-star-inserted"> Sao Tome and Principe <!----><!----><!----></option><option value="196: SA" class="ng-star-inserted"> Saudi Arabia <!----><!----><!----></option><option value="197: SN" class="ng-star-inserted"> Senegal <!----><!----><!----></option><option value="198: RS" class="ng-star-inserted"> Serbia <!----><!----><!----></option><option value="199: SC" class="ng-star-inserted"> Seychelles <!----><!----><!----></option><option value="200: SL" class="ng-star-inserted"> Sierra Leone <!----><!----><!----></option><option value="201: SG" class="ng-star-inserted"> Singapore <!----><!----><!----></option><option value="202: SX" class="ng-star-inserted"> Sint Maarten (Dutch part) <!----><!----><!----></option><option value="203: SK" class="ng-star-inserted"> Slovakia <!----><!----><!----></option><option value="204: SI" class="ng-star-inserted"> Slovenia <!----><!----><!----></option><option value="205: SB" class="ng-star-inserted"> Solomon Islands <!----><!----><!----></option><option value="206: SO" class="ng-star-inserted"> Somalia <!----><!----><!----></option><option value="207: ZA" class="ng-star-inserted"> South Africa <!----><!----><!----></option><option value="208: GS" class="ng-star-inserted"> South Georgia and the South Sandwich Islands <!----><!----><!----></option><option value="209: SS" class="ng-star-inserted"> South Sudan <!----><!----><!----></option><option value="210: ES" class="ng-star-inserted"> Spain <!----><!----><!----></option><option value="211: LK" class="ng-star-inserted"> Sri Lanka <!----><!----><!----></option><option value="212: SD" class="ng-star-inserted"> Sudan <!----><!----><!----></option><option value="213: SR" class="ng-star-inserted"> Suriname <!----><!----><!----></option><option value="214: SJ" class="ng-star-inserted"> Svalbard and Jan Mayen <!----><!----><!----></option><option value="215: SE" class="ng-star-inserted"> Sweden <!----><!----><!----></option><option value="216: CH" class="ng-star-inserted"> Switzerland <!----><!----><!----></option><option value="217: SY" class="ng-star-inserted"> Syrian Arab Republic <!----><!----><!----></option><option value="218: TW" class="ng-star-inserted"> Taiwan <!----><!----><!----></option><option value="219: TJ" class="ng-star-inserted"> Tajikistan <!----><!----><!----></option><option value="220: TZ" class="ng-star-inserted"> Tanzania, the United Republic of <!----><!----><!----></option><option value="221: TH" class="ng-star-inserted"> Thailand <!----><!----><!----></option><option value="222: TL" class="ng-star-inserted"> Timor-Leste <!----><!----><!----></option><option value="223: TG" class="ng-star-inserted"> Togo <!----><!----><!----></option><option value="224: TK" class="ng-star-inserted"> Tokelau <!----><!----><!----></option><option value="225: TO" class="ng-star-inserted"> Tonga <!----><!----><!----></option><option value="226: TT" class="ng-star-inserted"> Trinidad and Tobago <!----><!----><!----></option><option value="227: TN" class="ng-star-inserted"> Tunisia <!----><!----><!----></option><option value="228: TR" class="ng-star-inserted"> Türkiye <!----><!----><!----></option><option value="229: TM" class="ng-star-inserted"> Turkmenistan <!----><!----><!----></option><option value="230: TC" class="ng-star-inserted"> Turks and Caicos Islands <!----><!----><!----></option><option value="231: TV" class="ng-star-inserted"> Tuvalu <!----><!----><!----></option><option value="232: UG" class="ng-star-inserted"> Uganda <!----><!----><!----></option><option value="233: UA" class="ng-star-inserted"> Ukraine <!----><!----><!----></option><option value="234: AE" class="ng-star-inserted"> United Arab Emirates <!----><!----><!----></option><option value="235: GB" class="ng-star-inserted"> United Kingdom of Great Britain and Northern Ireland <!----><!----><!----></option><option value="236: UM" class="ng-star-inserted"> United States Minor Outlying Islands <!----><!----><!----></option><option value="237: US" class="ng-star-inserted"> United States of America <!----><!----><!----></option><option value="238: UY" class="ng-star-inserted"> Uruguay <!----><!----><!----></option><option value="239: UZ" class="ng-star-inserted"> Uzbekistan <!----><!----><!----></option><option value="240: VU" class="ng-star-inserted"> Vanuatu <!----><!----><!----></option><option value="241: VE" class="ng-star-inserted"> Venezuela (Bolivarian Republic of) <!----><!----><!----></option><option value="242: VN" class="ng-star-inserted"> Viet Nam <!----><!----><!----></option><option value="243: VG" class="ng-star-inserted"> Virgin Islands (British) <!----><!----><!----></option><option value="244: VI" class="ng-star-inserted"> Virgin Islands (U.S.) <!----><!----><!----></option><option value="245: WF" class="ng-star-inserted"> Wallis and Futuna <!----><!----><!----></option><option value="246: EH" class="ng-star-inserted"> Western Sahara <!----><!----><!----></option><option value="247: YE" class="ng-star-inserted"> Yemen <!----><!----><!----></option><option value="248: ZM" class="ng-star-inserted"> Zambia <!----><!----><!----></option><option value="249: ZW" class="ng-star-inserted"> Zimbabwe <!----><!----><!----></option>
                    </x-ui.forms.select>
                </x-ui.forms.group>
            </div>
        </form>
    </div>

    {{-- Discount section--}}
    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-8 my-8">
        <h1 class="mb-6 text-2xl">Discount</h1>

        <form wire:submit.prevent="applyDiscount" class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <x-ui.forms.group label="Discount Code" for="discountCode" error="discountError">
                    <x-ui.forms.input
                        name="discountCode"
                        id="discountCode"
                        wire:model="discountCode"
                        placeholder="Enter discount code"
                    />
                </x-ui.forms.group>
            </div>

            <div class="sm:mt-7">
                <x-ui.button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                >
                    Apply
                </x-ui.button>
            </div>
        </form>

        @if(count($appliedDiscounts) > 0)
            <div class="space-y-2">
                <h3 class="font-medium">Applied Discounts:</h3>
                <div>
                    @foreach($appliedDiscounts as $discount)
                        <div class="flex justify-between items-center py-2 border-b last:border-b-0 dark:border-gray-700">
                        <span>
                            {{ $discount->code }}
                        </span>
                            <div class="flex gap-8">
                            <span class="opacity-65">
                            {{ !empty($discount->discount_percent) ? $discount->discount_percent.'%' : '€'.number_format($discount->discount_fixed_cents/100, 2) }}
                            </span>
                                <x-ui.cross-button wire:click="removeDiscount({{ $discount->id }})" />
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        @endif
    </div>

    @include('partials.tenant-event.order-summary')

    @if (session()->has('message'))
        <x-ui.flash-message/>
    @endif

    <div class="flex justify-between mt-6">
        <div>
            <x-ui.button
                variant="secondary"
                wire:click="backToTickets"
                wire:loading.attr="disabled"
            >
                Back to tickets
            </x-ui.button>
        </div>

        <div>
            <x-ui.button
                type="submit"
                form="infoForm"
                variant="primary"
                wire:loading.attr="disabled"
                wire:target="proceedToPayment"
                :disabled="!$customer_id"
            >
                <span wire:loading.remove wire:target="proceedToPayment">
                    Proceed to payment
                </span>
                <span wire:loading wire:target="proceedToPayment">
                    Processing...
                </span>
            </x-ui.button>
        </div>
    </div>

</div>
