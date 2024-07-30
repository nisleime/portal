<div>

    @if (empty($invoices))
        <div class="alert alert-default">
            <p>Dados insuficiente</p>
        </div>
    @else
        <canvas id="invoices_qty_total" height="320"></canvas>
    @endif

</div>

@push('component-scripts')

    <script>
        document.addEventListener('livewire:load', function() {

            (function($) {

                // emite evento para inicializas atributos
                @this.emitTo('panel.dashboard.invoice-qty-total', 'eventInitAttributes');

                Livewire.on('eventInitChartQtyTotal', (invoices) => {

                    $.initChartQtyTotal = function() {

                        if (typeof CHART_INVOICES_QTY_TOTAL != 'undefined') {
                            CHART_INVOICES_QTY_TOTAL.destroy();
                        }

                        if (invoices.length) {

                            var models = invoices.map(d => d['model']);
                            var qtys = invoices.map(d => d['qty']);
                            var totals = invoices.map(d => d['total']);

                            // chart doughnut style
                            var invoices_qty_total = document.getElementById('invoices_qty_total').getContext('2d');

                            window.CHART_INVOICES_QTY_TOTAL = new Chart(invoices_qty_total, {
                                type: 'doughnut',
                                data: {
                                    labels: models,
                                    datasets: [{
                                        data: qtys,
                                        backgroundColor: [
                                            'rgba(26, 115, 232, 0.5)',
                                            'rgba(111, 125, 151, 0.5)',
                                            'rgba(135, 183, 184, 0.5)',
                                            'rgba(81, 87, 92, 0.5)',
                                            'rgba(218, 210, 206, 0.5)',
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: 'Quantidade/total por modelo'
                                        },
                                        tooltip: {
                                            callbacks: {
                                                footer: (tooltipItems) => {

                                                    var context = tooltipItems[0];

                                                    var total = Intl.NumberFormat(
                                                        'pt-br', {
                                                            style: 'currency',
                                                            currency: 'BRL'
                                                        }).format(totals[context
                                                        .dataIndex]);

                                                    return total;
                                                }
                                            }
                                        }
                                    }
                                }
                            });

                        }

                    }

                    $.initChartQtyTotal();

                });

            })(jQuery);

        });
    </script>

@endpush
