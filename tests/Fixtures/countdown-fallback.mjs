import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { runInNewContext } from 'node:vm'
const dataset = JSON.parse(readFileSync(0, 'utf8'))
const fields = new Map()
const root = {
    dataset,
    isConnected: true,
    querySelector(selector) {
        if (!fields.has(selector)) fields.set(selector, { textContent: '' })
        return fields.get(selector)
    },
}
let tick
const target = Date.parse(dataset.target)
let now
runInNewContext(readFileSync(process.argv[2], 'utf8'), {
    document: { querySelectorAll: () => [root], addEventListener: () => {} },
    Date: { parse: Date.parse, now: () => now ?? target - 59000 },
    Intl,
    window: {
        setInterval: (callback) => {
            tick = callback
            return 1
        },
        clearInterval() {},
    },
})
const expectations = {
    missing: [
        '0 days, 0 hours and 0 minutes remaining',
        '1 day, 1 hour and 1 minute remaining',
        '2 days, 2 hours and 2 minutes remaining',
    ],
    other: [
        '0 jours, 0 heures et 0 minutes restantes',
        '1 jours, 1 heures et 1 minutes restantes',
        '2 jours, 2 heures et 2 minutes restantes',
    ],
    partial: [
        '0 jour, 0 heures et 0 minutes restantes',
        '1 jour, 1 heures et 1 minute restantes',
        '2 days, 2 heures et 2 minutes restantes',
    ],
}
for (const [i, seconds] of [59, 90060, 180120].entries()) {
    now = target - seconds * 1000
    tick()
    assert.equal(
        root.querySelector('[data-countdown-status]').textContent,
        expectations[process.argv[3]][i],
    )
}
