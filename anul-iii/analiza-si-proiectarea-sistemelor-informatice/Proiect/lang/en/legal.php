<?php

return [
    'anpc_link' => 'ANPC: information about ADR',
    'website_link' => 'ANPC website',
    'sal_link' => 'Consumer ADR platform',
    'order_consent' => 'I have read and accept the Terms and Conditions. I understand this demo order does not activate a service or transfer money.',
    'draft_notice' => 'Demonstration documents. Replace bracketed fields with verified details and obtain legal review before publication or sales.',
    'company_fields' => 'Details to complete: [legal name], [legal form], [tax ID], [Trade Register number], [registered address], [email], [phone], [billing and VAT details].',
    'pages' => [
        'terms' => [
            'title' => 'Terms and conditions',
            'intro' => 'This template outlines the general framework for ELOQUENCE HOSTING services. Complete and review the final commercial terms before use.',
            'sections' => [
                ['title' => 'Provider', 'body' => 'Services are offered by [legal name], [legal form], tax ID [number], registered with the Trade Register under [number], with registered office at [address]. Contact: [email] / [phone].'],
                ['title' => 'Services and orders', 'body' => 'The catalogue may include web hosting, managed WordPress, VPS, cloud, email, domains, backups, and SSL certificates. An order becomes binding only after provider confirmation and communication of the service-specific terms. This site is a demonstration: displayed orders and payments do not activate commercial services or transfer money.'],
                ['title' => 'Prices, taxes, and billing', 'body' => 'The price, currency, term, applicable taxes, billing frequency, renewal, and any additional charges must be clearly shown before an order is placed. Values in this demonstration are illustrative. Complete the actual VAT treatment and billing rules: [price and tax details].'],
                ['title' => 'Use and availability', 'body' => 'Customers must use services lawfully and keep access credentials secure. Resources, limits, maintenance, support, backups, and availability commitments must be stated in each plan or a separate SLA; no uptime or backup policy is promised here.'],
                ['title' => 'Termination and disputes', 'body' => 'Term, cancellation, suspension, refunds, and termination effects must be completed for each service: [procedure and timelines]. Consumers retain their statutory rights. For complaints, contact [contact address]; see the Complaints page for further information.'],
            ],
        ],
        'privacy' => [
            'title' => 'Privacy notice',
            'intro' => 'This notice is a template for personal data processing related to accounts, orders, billing, and support.',
            'sections' => [
                ['title' => 'Controller and contact', 'body' => 'Controller: [legal name], [address], tax ID [number]. Privacy contact: [privacy email]. Data Protection Officer, if appointed: [contact details].'],
                ['title' => 'Data, purposes, and legal bases', 'body' => 'Data may include identity and contact details, account data, orders and services, invoices and payments, and support conversations. Map each purpose to its legal basis: account and contract administration [basis], accounting and tax duties [basis], security and abuse prevention [legitimate interest and assessment], and marketing only with the required legal basis and choice.'],
                ['title' => 'Recipients and transfers', 'body' => 'The app also loads fonts from Google Fonts. Include this provider and any applicable data transfers in the recipient list before publication. List processors (hosting, email, payments, support, accounting), their details, and any transfers outside the EEA with applicable safeguards: [list and details].'],
                ['title' => 'Retention and individual rights', 'body' => 'Set specific retention periods for accounts, financial records, tickets, and logs: [periods]. Individuals may request access, rectification, erasure, restriction, and data portability where applicable, object to processing based on legitimate interests, withdraw consent where it is the basis, and lodge a complaint with the Romanian data protection authority (ANSPDCP). Contact: [privacy email].'],
                ['title' => 'Security and updates', 'body' => 'Technical and organisational measures proportionate to risk are applied. Describe measures actually implemented and update this notice when processing changes.'],
            ],
        ],
        'cookies' => [
            'title' => 'Cookie policy',
            'intro' => 'This demonstration app uses technical cookies required for the session and form protection. The visual theme is stored locally in the browser. No analytics or advertising cookies are currently configured.',
            'sections' => [
                ['title' => 'Necessary cookies', 'body' => 'Session cookies and the CSRF token support authentication, navigation, and request protection. They are technical and are not used for advertising profiles.'],
                ['title' => 'Theme preference', 'body' => 'The light/dark preference is stored in the browser local storage, not in a cookie.'],
                ['title' => 'Optional services', 'body' => 'If analytics, advertising, maps, chat, or third-party content are added, update the provider list, purposes, durations, and consent mechanism before activation.'],
            ],
        ],
        'withdrawal' => [
            'title' => 'Right of withdrawal',
            'intro' => 'Information for consumers entering distance contracts for services. Applicability and calculation of the period depend on the contract and circumstances.',
            'sections' => [
                ['title' => 'Period and notice', 'body' => 'Where provided by law, a consumer may withdraw from a service contract within 14 days of its conclusion without giving a reason, by sending an unequivocal statement to [email/address]. The model form below may be used but is not mandatory.'],
                ['title' => 'Starting service during the withdrawal period', 'body' => 'If a consumer expressly requests performance to begin before the withdrawal period expires, information about proportional payment and loss of the right after full performance must be separately presented and acknowledged in the form required by law. Do not apply a general waiver or automatic no-refund term. Statutory exceptions apply only when their conditions are met.'],
                ['title' => 'Effects and refunds', 'body' => 'Where withdrawal is validly exercised, refunds and any proportional amounts for services already started are calculated under the law and information given before contract. Complete the operational refund method and deadline: [procedure].'],
                ['title' => 'Model withdrawal form', 'body' => 'To [legal name, address, and email]: I hereby give notice that I withdraw from my contract for the following service: [service]. Ordered/contracted on: [date]. Name: [name]. Address: [address]. Notice date: [date]. Signature (only for paper notice): [signature].'],
            ],
        ],
        'complaints' => [
            'title' => 'Complaints and dispute resolution',
            'intro' => 'First send a complaint to the provider with the order or ticket number and a clear description of the issue.',
            'sections' => [
                ['title' => 'Contact the provider', 'body' => 'Email: [support address]. Postal address: [provider address]. Phone: [number]. Response stages and timing: [internal procedure]. Keep proof of submission and relevant documents.'],
                ['title' => 'Authorities and ADR', 'body' => 'Consumers can consult ANPC information about alternative dispute resolution (SAL) and verify eligibility and the applicable procedure. The SAL platform is at reclamatiisal.anpc.ro; general information is at anpc.ro/sal/. Submitting a request does not override statutory admissibility conditions.'],
                ['title' => 'Trader details', 'body' => 'Before publication, complete legal identity, tax ID, registration number, registered office, and contact details in the Terms and Conditions and on this page.'],
            ],
        ],
    ],
];
