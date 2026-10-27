import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { runInNewContext } from 'node:vm'
const dataset = JSON.parse(readFileSync(0, 'utf8'))
const fields = new Map()
const root = {
    dataset,
    isConnected: true,
    querySelector: (selector) => {
        if (!fields.has(selector)) fields.set(selector, { textContent: '' })
        return fields.get(selector)
    },
}
let tick
const target = Date.parse(dataset.target)
let now = target - (2 * 86400 + 2 * 3600 + 2 * 60) * 1000
runInNewContext(readFileSync(process.argv[2], 'utf8'), {
    document: { querySelectorAll: () => [root], addEventListener: () => {} },
    Date: { parse: Date.parse, now: () => now },
    Intl,
    window: {
        setInterval: (callback) => {
            tick = callback
            return 1
        },
        clearInterval: () => {},
    },
})
const french = dataset.locale === 'fr'
assert.equal(
    root.querySelector('[data-countdown-status]').textContent,
    french
        ? '2 jours, 2 heures et 2 minutes restantes'
        : '2 days, 2 hours and 2 minutes remaining',
)
for (const [seconds, expected] of [
    [
        86400 + 3600 + 60,
        french
            ? '1 jour, 1 heure et 1 minute restantes'
            : '1 day, 1 hour and 1 minute remaining',
    ],
    [
        59,
        french
            ? '0 jour, 0 heure et 0 minute restantes'
            : '0 days, 0 hours and 0 minutes remaining',
    ],
    [0, 'Terminé <test>'],
]) {
    now = target - seconds * 1000
    tick()
    assert.equal(
        root.querySelector('[data-countdown-status]').textContent,
        expected,
    )
}
