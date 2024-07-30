<div>

    @if (empty($invoices))
        <div class="alert alert-default">
            <p>Dados insuficiente</p>
        </div>
    @else
        <canvas id="invoices_per_month" height="320"></canvas>
    @endif

</div>

@push('component-scripts')

    <script>
        document.addEventListener('livewire:load', function() {

            (function($) {

                // emite evento para inicializas atributos
                @this.emitTo('panel.dashboard.invoice-per-month', 'eventInitAttributes');

                Livewire.on('eventInitChartQtyPerMonth', (invoices) => {

                    $.initChartQtyPerMonth = function() {

                        if (typeof CHART_INVOICS_PER_MONTH != 'undefined') {
                            CHART_INVOICS_PER_MONTH.destroy();
                        }

                        if (invoices.length) {

                            var months = invoices.map(d => d['month']);
                            var nfe = invoices.map(d => d['55']);
                            var cte = invoices.map(d => d['57']);
                            var mdfe = invoices.map(d => d['58']);
                            var cfesat = invoices.map(d => d['59']);
                            var nfce = invoices.map(d => d['65']);

                            // chart invoices_per_month style
                            var invoices_per_month = document.getElementById('invoices_per_month').getContext('2d');

                            window.CHART_INVOICS_PER_MONTH = new Chart(invoices_per_month, {
                                type: 'bar',
                                data: {
                                    labels: months,
                                    datasets: [{
                                            label: 'NF-e',
                                            data: nfe,
                                            backgroundColor: "rgba(26, 115, 232, 0.5)",
                                        },
                                        {
                                            label: 'CT-e',
                                            data: cte,
                                            backgroundColor: "rgba(81, 87, 92, 0.5)",
                                        },
                                        {
                                            label: 'MDF-e',
                                            data: mdfe,
                                            backgroundColor: "rgba(218, 210, 206, 0.5)",
                                        },
                                        {
                                            label: 'Entrada',
                                            data: cfesat,
                                            backgroundColor: "rgba(111, 125, 151, 0.5)",
                                        },
                                        {
                                            label: 'NFC-e',
                                            data: nfce,
                                            backgroundColor: "rgba(135, 183, 184, 0.5)",
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: 'Quantidade por mes'
                                        }
                                    },
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                }
                            });

                        }

                    }

                    $.initChartQtyPerMonth();

                });

            })(jQuery);

        });
    </script>

@endpush
