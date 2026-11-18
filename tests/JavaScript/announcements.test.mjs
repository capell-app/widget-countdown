import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import test from 'node:test'
import vm from 'node:vm'

const source = readFileSync(
    new URL('../../resources/dist/widget.js', import.meta.url),
    'utf8',
)
const target = Date.parse('2026-12-01T12:00:00Z')

function countdown(remainingSeconds) {
    let now = target - remainingSeconds * 1000
    let tick
    const fields = new Map()
    const writes = []
    const field = (selector) => {
        if (!fields.has(selector)) {
            let text = ''
            fields.set(selector, {
                get textContent() {
                    return text
                },
                set textContent(value) {
                    text = value
                    writes.push([selector, value])
                },
            })
        }
        return fields.get(selector)
    }
    const root = {
        dataset: {
            target: '2026-12-01T12:00:00Z',
            locale: 'en',
            remainingLabel: ':days, :hours and :minutes remaining',
            expiredLabel: 'Finished',
        },
        isConnected: true,
        querySelector: field,
    }
    vm.runInNewContext(source, {
        document: { querySelectorAll: () => [root], addEventListener() {} },
        Date: { parse: Date.parse, now: () => now },
        Intl,
        window: {
            setInterval: (callback) => {
                tick = callback
                return 1
            },
            clearInterval() {},
        },
    })
    return {
        status: () => field('[data-countdown-status]').textContent,
        statusWrites: () =>
            writes.filter(
                ([selector]) => selector === '[data-countdown-status]',
            ),
        advance: (seconds) => {
            now += seconds * 1000
            tick()
        },
        minutes: () => field('[data-minutes]').textContent,
    }
}

test('the status is announced once on load and not again on minute ticks', () => {
    const widget = countdown(2 * 3600 + 5 * 60 + 10)
    const initial = widget.status()
    assert.equal(initial, '0 days, 2 hours and 5 minutes remaining')
    for (let minute = 0; minute < 5; minute++) widget.advance(60)
    assert.equal(widget.status(), initial)
    assert.equal(widget.statusWrites().length, 1)
    // The visual values keep counting down.
    assert.equal(widget.minutes(), '0')
})

test('expiry is announced once', () => {
    const widget = countdown(90)
    widget.advance(30)
    widget.advance(60)
    assert.equal(widget.status(), 'Finished')
    assert.equal(widget.statusWrites().length, 2)
})
