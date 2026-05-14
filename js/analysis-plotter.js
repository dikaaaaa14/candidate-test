'use strict';

class AnalysisPlotter {
    constructor(canvasId) {
        this.canvasId = canvasId;
        this._chart = null;
    }

    plot(data) {
        var equation = data.equation;
        var beam     = data.beam;
        var L1       = beam.primarySpan || 0;
        var L2       = beam.secondarySpan || 0;
        var totalL   = L1 + (isNaN(L2) ? 0 : L2);

        var points = 100;
        var labels = [];
        var values = [];

        for (var i = 0; i <= points; i++) {
            var x      = (i / points) * totalL;
            var result = equation(x);
            labels.push(x.toFixed(2));
            values.push(parseFloat(result.y.toFixed(4)));
        }

        var ctx = document.getElementById(this.canvasId).getContext('2d');
        if (this._chart) this._chart.destroy();

        this._chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: this.canvasId.replace(/_/g, ' ').toUpperCase(),
                    data: values,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0,0,255,0.05)',
                    fill: true,
                    pointRadius: 0,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: { display: true, text: 'Position x (m)' },
                        ticks: { maxTicksLimit: 10 }
                    },
                    y: {
                        title: { display: true, text: 'Value' }
                    }
                }
            }
        });
    }
}