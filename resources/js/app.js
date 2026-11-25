import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Chart.defaults.font.family = "'Inter Variable', 'Inter', system-ui, sans-serif";
Chart.defaults.font.size = 11;
Chart.defaults.color = '#7d91a5';
Chart.defaults.borderColor = '#e8f0f7';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 7;
Chart.defaults.plugins.legend.labels.boxHeight = 7;
Chart.defaults.plugins.legend.labels.padding = 14;
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(8, 43, 76, 0.94)';
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.titleFont = { size: 11, weight: '600' };
Chart.defaults.plugins.tooltip.bodyFont = { size: 11 };

function buildChartConfig(payload) {
    const type = payload.type || 'line';
    const colors = payload.colors || ['#1689e8', '#20b96b', '#f2a52b', '#8798a9', '#299ff2'];

    if (type === 'doughnut') {
        return {
            type: 'doughnut',
            data: {
                labels: payload.labels || [],
                datasets: [{
                    data: payload.values || [],
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: payload.cutout || '68%',
                plugins: {
                    legend: {
                        position: payload.legendPosition || 'right',
                        labels: { boxWidth: 8, boxHeight: 8, padding: 12, font: { size: 11 } },
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((sum, value) => sum + Number(value || 0), 0);
                                const value = Number(ctx.raw || 0);
                                const pct = total > 0 ? ((value / total) * 100).toFixed(0) : '0';
                                return ` ${ctx.label}: ${value} (${pct}%)`;
                            },
                        },
                    },
                },
                ...(payload.options || {}),
            },
        };
    }

    const datasets = (payload.datasets || [{
        label: payload.label || 'Value',
        data: payload.values || [],
        color: colors[0],
        fill: payload.fill ?? (type === 'line'),
    }]).map((dataset, index) => ({
        label: dataset.label,
        data: dataset.data,
        borderColor: dataset.color || colors[index % colors.length],
        backgroundColor: dataset.fillColor || (type === 'bar'
            ? (dataset.color || colors[index % colors.length])
            : 'rgba(41, 159, 242, 0.10)'),
        borderWidth: type === 'bar' ? 0 : 2.2,
        borderRadius: type === 'bar' ? 5 : 0,
        barPercentage: 0.55,
        categoryPercentage: 0.72,
        pointRadius: payload.pointRadius ?? (type === 'line' ? 3 : 0),
        pointHoverRadius: 4,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: dataset.color || colors[index % colors.length],
        pointBorderWidth: 2,
        tension: payload.tension ?? 0.38,
        fill: dataset.fill ?? (type === 'line'),
    }));

    return {
        type,
        data: { labels: payload.labels || [], datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { maxRotation: 0, autoSkipPadding: 12 },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#eef4fa' },
                    border: { display: false },
                    ticks: {
                        precision: 0,
                        callback: payload.yCallback ? (value) => payload.yCallback.replace('{value}', value) : undefined,
                    },
                },
            },
            plugins: {
                legend: { display: payload.showLegend ?? (datasets.length > 1), position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: payload.tooltipSuffix
                            ? (ctx) => ` ${ctx.dataset.label}: ${ctx.formattedValue} ${payload.tooltipSuffix}`
                            : undefined,
                    },
                },
            },
            ...(payload.options || {}),
        },
    };
}

function renderCharts(root = document) {
    root.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        if (canvas.dataset.chartRendered === 'true') {
            return;
        }

        try {
            const payload = JSON.parse(canvas.dataset.chart || '{}');
            new Chart(canvas, buildChartConfig(payload));
            canvas.dataset.chartRendered = 'true';
        } catch (error) {
            console.error('Chart configuration error', error);
        }
    });
}

window.renderCharts = renderCharts;

document.addEventListener('DOMContentLoaded', () => renderCharts());

document.addEventListener('alpine:init', () => {
    Alpine.store('toasts', {
        items: [],
        push(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.dismiss(id), 4500);
        },
        dismiss(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
    });

    Alpine.data('flashToast', (message, type = 'success') => ({
        init() {
            if (message) {
                this.$store.toasts.push(message, type);
            }
        },
    }));

    Alpine.data('confirmAction', () => ({
        open: false,
        message: '',
        action: '',
        method: 'POST',
        confirmLabel: 'Confirm',
        show(message, action, method = 'POST', confirmLabel = 'Confirm') {
            this.message = message;
            this.action = action;
            this.method = method;
            this.confirmLabel = confirmLabel;
            this.open = true;
        },
        submit() {
            const form = this.$refs.confirmForm;
            form.action = this.action;
            form.method = 'POST';
            form.querySelector('[name="_method"]').value = this.method;
            form.submit();
        },
    }));

    Alpine.data('acControl', () => ({
        config: window.__AC_CONTROL__ || {},
        reportedPower: null,
        reportedSetpoint: null,
        reportedAt: null,
        reportedQuality: null,
        desiredPower: null,
        desiredSetpoint: null,
        sending: false,
        error: '',
        sendTimer: null,

        init() {
            this.reportedPower = this.config.power || 'unknown';
            this.reportedSetpoint = this.config.setpoint;
            this.reportedAt = this.config.observedAt || null;
            this.reportedQuality = this.config.quality || 'unknown';
            this.desiredPower = this.config.desired_power ?? null;
            this.desiredSetpoint = this.config.desired_setpoint ?? null;
        },

        get isOn() {
            return this.desiredPower === 'on';
        },

        get displayPower() {
            if (! this.reportedPower || this.reportedPower === 'unknown') {
                return 'Unknown';
            }
            if (this.reportedPower === 'stale') {
                return 'Stale';
            }
            return this.reportedPower === 'known_on' ? 'ON' : 'OFF';
        },

        get displayDesiredPower() {
            if (! this.desiredPower) {
                return '—';
            }
            return this.desiredPower === 'on' ? 'ON' : 'OFF';
        },

        get dialPercent() {
            if (this.reportedSetpoint === null || this.config.min === null || this.config.max === null) {
                return 0;
            }
            const range = Math.max(1, this.config.max - this.config.min);
            return Math.max(4, Math.min(100, ((this.reportedSetpoint - this.config.min) / range) * 100));
        },

        get desiredDialPercent() {
            if (this.desiredSetpoint === null || this.config.min === null || this.config.max === null) {
                return 0;
            }
            const range = Math.max(1, this.config.max - this.config.min);
            return Math.max(4, Math.min(100, ((this.desiredSetpoint - this.config.min) / range) * 100));
        },

        get canDecrease() {
            return this.config.canControl && this.desiredSetpoint !== null && this.desiredSetpoint > this.config.min;
        },

        get canIncrease() {
            return this.config.canControl && this.desiredSetpoint !== null && this.desiredSetpoint < this.config.max;
        },

        setPower(position) {
            if (! this.config.canControl || this.sending) {
                return;
            }

            this.desiredPower = position;
            this.send({ power: position }, position === 'on' ? 'Requested ON' : 'Requested OFF');
        },

        changeSetpoint(delta) {
            if (! this.config.canControl || this.sending) {
                return;
            }

            const step = Number(this.config.step || 1);
            const base = this.desiredSetpoint ?? this.reportedSetpoint;

            if (base === null) {
                this.error = 'No last-reported setpoint is available. Send a full state command from the device first.';
                return;
            }

            const next = Math.min(this.config.max, Math.max(this.config.min, base + (delta * step)));

            if (next === base) {
                return;
            }

            this.desiredSetpoint = next;
            this.send({ temperature_setpoint_c: next }, `Requested setpoint ${next}°C`);
        },

        async send(desiredState, label) {
            this.sending = true;
            this.message = label;
            this.error = '';

            try {
                const response = await fetch(this.config.commandUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ desired_state: desiredState }),
                });

                const payload = await response.json();

                if (! response.ok) {
                    const errors = payload?.error?.details || {};
                    const firstError = Object.values(errors)[0];
                    throw new Error(Array.isArray(firstError) ? firstError[0] : (payload?.error?.message || 'The command could not be queued.'));
                }

                // Optimistically update desired state immediately.
                // The backend persists desired state; the device will pick it up
                // on its next heartbeat. We do not wait for device acknowledgement.
                if (desiredState.power !== undefined) {
                    this.desiredPower = desiredState.power;
                }
                if (desiredState.temperature_setpoint_c !== undefined) {
                    this.desiredSetpoint = desiredState.temperature_setpoint_c;
                }

                this.message = label;
                this.$store.toasts.push(label, 'success');
            } catch (error) {
                this.error = error.message || 'The command could not be sent.';
                this.$store.toasts.push(this.error, 'error');
            } finally {
                this.sending = false;
            }
        },
    }));

    Alpine.data('acFilters', () => ({
        q: '',
        site_id: null,
        building_id: null,
        floor_id: null,
        space_id: null,
        state: '',
        connectivity: '',
        sites: [],
        buildings: [],
        floors: [],
        spaces: [],
        currentView: 'grid',
        loading: false,
        debounceTimer: null,

        init() {
            const data = window.__acsFilters || {};
            this.q = data.q || '';
            this.site_id = data.site_id || null;
            this.building_id = data.building_id || null;
            this.floor_id = data.floor_id || null;
            this.space_id = data.space_id || null;
            this.state = data.state || '';
            this.connectivity = data.connectivity || '';
            this.sites = data.sites || [];
            this.buildings = data.buildings || [];
            this.floors = data.floors || [];
            this.spaces = data.spaces || [];
            this.currentView = data.currentView || 'grid';
            this._gridUrl = data.gridUrl || '/air-conditioners';
            this._tableUrl = data.tableUrl || '/air-conditioners?view=table';

            // Reset invalid descendant selections on load
            if (this.site_id && !this.sites.find(s => s.id == this.site_id)) this.site_id = null;
            if (this.building_id && !this.filteredBuildings.find(b => b.id == this.building_id)) this.building_id = null;
            if (this.floor_id && !this.filteredFloors.find(f => f.id == this.floor_id)) this.floor_id = null;
            if (this.space_id && !this.filteredSpaces.find(s => s.id == this.space_id)) this.space_id = null;
        },

        get filteredBuildings() {
            if (!this.site_id) return this.buildings;
            return this.buildings.filter(b => b.site_id == this.site_id);
        },

        get filteredFloors() {
            if (!this.building_id) return this.floors;
            return this.floors.filter(f => f.building_id == this.building_id);
        },

        get filteredSpaces() {
            if (!this.floor_id) return this.spaces;
            return this.spaces.filter(s => s.floor_id == this.floor_id);
        },

        async fetchResults() {
            this.loading = true;
            const baseUrl = window.__acsFilters?.routeUrl || '/air-conditioners';
            const params = new URLSearchParams({ partial: '1', view: this.currentView });
            if (this.q) params.set('q', this.q);
            if (this.site_id) params.set('site_id', this.site_id);
            if (this.building_id) params.set('building_id', this.building_id);
            if (this.floor_id) params.set('floor_id', this.floor_id);
            if (this.space_id) params.set('space_id', this.space_id);
            if (this.state) params.set('state', this.state);
            if (this.connectivity) params.set('connectivity', this.connectivity);

            try {
                const response = await fetch(baseUrl + '?' + params.toString());
                const html = await response.text();
                const container = document.getElementById('results-container');
                if (container) container.innerHTML = html;
                if (window.renderCharts) window.renderCharts();
                if (window.Alpine) window.Alpine.initTree(container);
            } catch (error) {
                console.error('Failed to fetch filtered results', error);
            } finally {
                this.loading = false;
            }
        },

        debounceSearch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.fetchResults(), 300);
        },

        resetFilters() {
            this.q = '';
            this.site_id = null;
            this.building_id = null;
            this.floor_id = null;
            this.space_id = null;
            this.state = '';
            this.connectivity = '';
            this.fetchResults();
        },

        get gridUrl() {
            return this._gridUrl;
        },

        get tableUrl() {
            return this._tableUrl;
        },
    }));
});

Alpine.start();
