<div>

    <div class="col-20 mb-30">

        <div class="dash-info-icon blue">
            <div class="col">
                <span class="big-text">NF-e</span>
                <span class="small-text">R$ {{ number_format($total_nfe, 2, ',', '.') }}</span>
                <small>QTD: {{ number_format($qty_nfe, 0, '.', '.') }}</small>
            </div>
            <div class="col">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
            </div>
        </div>

    </div>

    <div class="col-20 mb-30">

        <div class="dash-info-icon green">
            <div class="col">
                <span class="big-text">NFC-e</span>
                <span class="small-text">R$ {{ number_format($total_nfce, 2, ',', '.') }}</span>
                <small>QTD: {{ number_format($qty_nfce, 0, '.', '.') }}</small>
            </div>
            <div class="col">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
            </div>
        </div>

    </div>

    <div class="col-20 mb-30">

        <div class="dash-info-icon yellow">
            <div class="col">
                <span class="big-text">CT-e</span>
                <span class="small-text">R$ {{ number_format($total_cte, 2, ',', '.') }}</span>
                <small>QTD: {{ number_format($qty_cte, 0, '.', '.') }}</small>
            </div>
            <div class="col">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
            </div>
        </div>

    </div>

    <div class="col-20 mb-30">

        <div class="dash-info-icon red">
            <div class="col">
                <span class="big-text">MDF-e</span>
                <span class="small-text">R$ {{ number_format($total_mdfe, 2, ',', '.') }}</span>
                <small>QTD: {{ number_format($qty_mdfe, 0, '.', '.') }}</small>
            </div>
            <div class="col">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
            </div>
        </div>

    </div>

    <div class="col-20 mb-30">

        <div class="dash-info-icon ocean">
            <div class="col">
                <span class="big-text">NF-e Entrada</span>
                <span class="small-text">R$ {{ number_format($total_cfesat, 2, ',', '.') }}</span>
                <small>QTD: {{ number_format($qty_cfesat, 0, '.', '.') }}</small>
            </div>
            <div class="col">
                <span class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </span>
            </div>
        </div>

    </div>

</div>
