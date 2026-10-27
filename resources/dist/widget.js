;(() => {
    const mounted = new WeakMap()
    const initialise = (root) => {
        if (mounted.has(root)) return
        const target = Date.parse(root.dataset.target)
        if (!Number.isFinite(target)) return
        // Deferred scripts can run on markup rendered before the deployment.
        const legacyLabels = Object.fromEntries(
            ['day', 'hour', 'minute'].map((unit) => [
                unit,
                [
                    {
                        locale: 'en',
                        forms: {
                            one: `:count ${unit}`,
                            other: `:count ${unit}s`,
                        },
                    },
                ],
            ]),
        )
        const labels = root.dataset.unitLabels
            ? JSON.parse(root.dataset.unitLabels)
            : legacyLabels
        const unitLabel = (unit, count) => {
            const candidates = labels[unit] ?? legacyLabels[unit]
            // Also accept the flat category maps shipped before language-aware fallback.
            const translations = Array.isArray(candidates)
                ? candidates
                : [{ locale: root.dataset.locale ?? 'en', forms: candidates }]
            for (const { locale, forms } of translations) {
                let plurals
                try {
                    plurals = new Intl.PluralRules(locale)
                } catch {
                    plurals = new Intl.PluralRules('en')
                }
                const label = forms[plurals.select(count)] ?? forms.other
                if (label !== undefined)
                    return label.replaceAll(':count', String(count))
            }
            const forms = legacyLabels[unit][0].forms
            return (count === 1 ? forms.one : forms.other).replaceAll(
                ':count',
                String(count),
            )
        }
        let announcedMinute = null
        let timer
        const stop = () => {
            window.clearInterval(timer)
            mounted.delete(root)
        }
        const update = () => {
            if (!root.isConnected) {
                stop()
                return
            }
            const remaining = Math.max(0, target - Date.now())
            const totalSeconds = Math.floor(remaining / 1000)
            const days = Math.floor(totalSeconds / 86400)
            const hours = Math.floor((totalSeconds % 86400) / 3600)
            const minutes = Math.floor((totalSeconds % 3600) / 60)
            const seconds = totalSeconds % 60
            root.querySelector('[data-days]').textContent = String(days)
            root.querySelector('[data-hours]').textContent = String(hours)
            root.querySelector('[data-minutes]').textContent = String(minutes)
            root.querySelector('[data-seconds]').textContent = String(seconds)
            const status = root.querySelector('[data-countdown-status]')
            if (remaining === 0) {
                status.textContent = root.dataset.expiredLabel
                stop()
                return false
            }
            const minute = Math.floor(totalSeconds / 60)
            if (minute !== announcedMinute) {
                announcedMinute = minute
                const values = {
                    days: unitLabel('day', days),
                    hours: unitLabel('hour', hours),
                    minutes: unitLabel('minute', minutes),
                }
                status.textContent = (
                    root.dataset.remainingLabel ??
                    ':days, :hours and :minutes remaining'
                ).replace(/:(days|hours|minutes)\b/g, (_, unit) => values[unit])
            }
            return true
        }
        mounted.set(root, true)
        if (update()) timer = window.setInterval(update, 1000)
    }
    const mount = (root) => {
        if (root.matches?.('[data-capell-widget-countdown]')) initialise(root)
        root.querySelectorAll?.('[data-capell-widget-countdown]').forEach(
            initialise,
        )
    }
    mount(document)
    document.addEventListener('capell:content-ready', (event) =>
        mount(event.target),
    )
})()
