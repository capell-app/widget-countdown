document.querySelectorAll('[data-capell-widget-countdown]').forEach((root) => {
    const target = Date.parse(root.dataset.target)
    let announcedMinute = null
    let timer
    const update = () => {
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
            window.clearInterval(timer)
            return
        }
        const minute = Math.floor(totalSeconds / 60)
        if (minute !== announcedMinute) {
            announcedMinute = minute
            status.textContent = `${days} days, ${hours} hours and ${minutes} minutes remaining`
        }
    }
    update()
    timer = window.setInterval(update, 1000)
})
