/**
 * One Blood Bank - small progressive enhancements.
 * Everything here must work without a build step in production fallback.
 */

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

/* ------------------------------------------------------------------ */
/* Mobile navigation                                                   */
/* ------------------------------------------------------------------ */
document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.getElementById(button.dataset.menuToggle);
        if (!target) return;
        const isHidden = target.classList.toggle('hidden');
        button.setAttribute('aria-expanded', String(!isHidden));
    });
});

/* ------------------------------------------------------------------ */
/* Cascading location selects                                          */
/* ------------------------------------------------------------------ */
document.querySelectorAll('[data-location-form]').forEach((form) => {
    const division = form.querySelector('[data-location="division"]');
    const district = form.querySelector('[data-location="district"]');
    const upazila = form.querySelector('[data-location="upazila"]');
    const union = form.querySelector('[data-location="union"]');

    const urlFor = (element) => element?.dataset.url;
    const label = (element) => element?.dataset.name ?? 'name';

    const load = async (element, url, placeholder) => {
        if (!element || !url) return [];
        element.innerHTML = `<option value="">${placeholder}</option>`;
        element.disabled = true;

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) return [];
            const rows = await response.json();

            rows.forEach((row) => {
                const option = document.createElement('option');
                option.value = row.id;
                option.textContent = row[label(element)];
                element.appendChild(option);
            });

            return rows;
        } catch (error) {
            return [];
        } finally {
            element.disabled = false;
        }
    };

    if (division && district) {
        division.addEventListener('change', async () => {
            const template = district.dataset.urlTemplate;
            await load(
                district,
                template?.replace('__ID__', division.value),
                district.dataset.placeholder ?? 'জেলা নির্বাচন করুন',
            );
            if (upazila) {
                upazila.innerHTML = `<option value="">${upazila.dataset.placeholder ?? 'উপজেলা নির্বাচন করুন'}</option>`;
            }
            if (union) {
                union.innerHTML = `<option value="">${union.dataset.placeholder ?? 'ইউনিয়ন নির্বাচন করুন'}</option>`;
            }
        });
    }

    if (district && upazila) {
        district.addEventListener('change', async () => {
            const template = upazila.dataset.urlTemplate;
            await load(
                upazila,
                template?.replace('__ID__', district.value),
                upazila.dataset.placeholder ?? 'উপজেলা নির্বাচন করুন',
            );
            if (union) {
                union.innerHTML = `<option value="">${union.dataset.placeholder ?? 'ইউনিয়ন নির্বাচন করুন'}</option>`;
            }
        });
    }

    if (upazila && union) {
        upazila.addEventListener('change', async () => {
            const template = union.dataset.urlTemplate;
            await load(
                union,
                template?.replace('__ID__', upazila.value),
                union.dataset.placeholder ?? 'ইউনিয়ন নির্বাচন করুন',
            );
        });
    }
});

/* ------------------------------------------------------------------ */
/* Additional donor contact numbers                                    */
/* ------------------------------------------------------------------ */
document.querySelectorAll('[data-contact-repeater]').forEach((repeater) => {
    const rows = repeater.querySelector('[data-contact-rows]');
    const template = repeater.querySelector('[data-contact-template]');
    const addButton = repeater.querySelector('[data-add-contact]');
    let nextIndex = Date.now();

    if (!rows || !template || !addButton) return;

    const bindRemove = (row) => {
        row.querySelector('[data-remove-contact]')?.addEventListener('click', () => {
            if (rows.querySelectorAll('[data-contact-row]').length <= 1) {
                row.querySelectorAll('input').forEach((input) => {
                    input.value = '';
                });
                return;
            }

            row.remove();
        });
    };

    rows.querySelectorAll('[data-contact-row]').forEach(bindRemove);

    addButton.addEventListener('click', () => {
        if (rows.querySelectorAll('[data-contact-row]').length >= 5) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        const row = wrapper.firstElementChild;

        if (!row) return;

        rows.appendChild(row);
        bindRemove(row);
        row.querySelector('input')?.focus();
    });
});

/* ------------------------------------------------------------------ */
/* Phone reveal dialog                                                 */
/* ------------------------------------------------------------------ */
const phoneModal = document.getElementById('phone-modal');

if (phoneModal) {
    const english = document.documentElement.lang === 'en';
    const nameEl = phoneModal.querySelector('[data-phone-name]');
    const listEl = phoneModal.querySelector('[data-phone-list]');
    const noteEl = phoneModal.querySelector('[data-phone-note]');
    const callEl = phoneModal.querySelector('[data-phone-call]');
    const copyEl = phoneModal.querySelector('[data-phone-copy]');
    const copyLabel = english ? 'Copy all numbers' : 'সব নম্বর কপি করুন';
    const copiedLabel = english ? 'Copied ✓' : 'কপি হয়েছে ✓';
    let currentPhones = [];

    const rowTemplate = (labelText, numberText, loading = false) => {
        const row = document.createElement('div');
        row.className = 'rounded-xl bg-slate-50 px-4 py-3 text-center';

        const label = document.createElement('p');
        label.className = 'text-[10px] font-semibold uppercase tracking-wide text-slate-400';
        label.textContent = labelText;

        const number = document.createElement('p');
        number.className = 'text-xl font-extrabold tracking-wider text-slate-900';
        number.textContent = numberText;

        row.append(label, number);

        if (!loading) {
            const call = document.createElement('a');
            call.href = `tel:${numberText}`;
            call.className = 'mt-1 inline-block text-xs font-semibold text-brand-700 hover:underline';
            call.textContent = english ? 'Call this number' : 'এই নম্বরে কল করুন';
            row.appendChild(call);
        }

        return row;
    };

    const renderPhones = (phones) => {
        listEl.innerHTML = '';
        phones.forEach((item, index) => {
            const label = item.label || (index === 0 ? (english ? 'Primary' : 'প্রধান নম্বর') : (english ? 'Other' : 'অন্যান্য'));
            listEl.appendChild(rowTemplate(label, item.phone));
        });

        const primary = phones[0]?.phone ?? '';
        callEl.href = primary ? `tel:${primary}` : '#';
        callEl.classList.toggle('pointer-events-none', !primary);
        callEl.classList.toggle('opacity-50', !primary);
    };

    const close = () => {
        phoneModal.classList.add('hidden');
        phoneModal.style.display = 'none';
    };

    phoneModal.querySelectorAll('[data-phone-close]').forEach((el) => {
        el.addEventListener('click', close);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') close();
    });

    document.querySelectorAll('[data-reveal-phone]').forEach((button) => {
        button.addEventListener('click', async () => {
            const url = button.dataset.revealPhone;

            phoneModal.classList.remove('hidden');
            phoneModal.style.display = 'flex';
            nameEl.textContent = button.dataset.donorName ?? (english ? 'Donor' : 'ডোনার');
            noteEl.textContent = '';
            copyEl.textContent = copyLabel;
            listEl.innerHTML = '';
            listEl.appendChild(rowTemplate(
                english ? 'Contacts' : 'যোগাযোগ',
                english ? 'Loading…' : 'লোড হচ্ছে…',
                true,
            ));
            callEl.href = '#';
            callEl.classList.add('pointer-events-none', 'opacity-50');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    listEl.innerHTML = '';
                    listEl.appendChild(rowTemplate(
                        english ? 'Not available' : 'নম্বর দেখানো যাচ্ছে না',
                        '—',
                        true,
                    ));
                    noteEl.textContent = data.message ?? (english ? 'Please try again shortly.' : 'একটু পর আবার চেষ্টা করুন।');
                    return;
                }

                currentPhones = Array.isArray(data.phones) && data.phones.length > 0
                    ? data.phones
                    : [{ label: english ? 'Primary' : 'প্রধান নম্বর', phone: data.phone }];

                renderPhones(currentPhones);

                if (data.remaining === null) {
                    noteEl.textContent = english
                        ? 'Unlimited phone reveals are enabled.'
                        : 'আনলিমিটেড নম্বর দেখার সুবিধা চালু আছে।';
                } else {
                    noteEl.textContent = data.remaining > 0
                        ? (english
                            ? `You can reveal ${data.remaining} more number(s) today.`
                            : `আজ আরও ${data.remaining} টি নম্বর দেখতে পারবেন।`)
                        : (english ? 'Your daily reveal limit has been reached.' : 'আজকের নম্বর দেখার সীমা শেষ।');
                }

                copyEl.onclick = async () => {
                    try {
                        await navigator.clipboard.writeText(
                            currentPhones.map((item) => `${item.label}: ${item.phone}`).join('\n'),
                        );
                        copyEl.textContent = copiedLabel;
                        setTimeout(() => (copyEl.textContent = copyLabel), 1800);
                    } catch (error) {
                        /* Clipboard may be unavailable on http origins. */
                    }
                };
            } catch (error) {
                listEl.innerHTML = '';
                listEl.appendChild(rowTemplate(
                    english ? 'Connection problem' : 'সংযোগে সমস্যা হয়েছে',
                    '—',
                    true,
                ));
                noteEl.textContent = english
                    ? 'Check your internet connection and try again.'
                    : 'ইন্টারনেট সংযোগ পরীক্ষা করে আবার চেষ্টা করুন।';
            }
        });
    });
}

/* ------------------------------------------------------------------ */
/* Auto-dismiss flash messages                                         */
/* ------------------------------------------------------------------ */
setTimeout(() => {
    document.querySelectorAll('[data-flash]').forEach((el) => {
        el.classList.add('opacity-0', 'transition', 'duration-500');
        setTimeout(() => el.remove(), 500);
    });
}, 7000);
