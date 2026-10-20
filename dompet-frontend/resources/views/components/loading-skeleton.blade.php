@props(['count' => 1])

<div>
    @for ($i = 0; $i < $count; $i++)
        <div class="tx" style="background:var(--cream);">
            <div class="tx-cat-icon" style="background:#fff;"></div>
            <div class="tx-info">
                <div class="tx-desc" style="background:rgba(17,16,16,.1);height:14px;width:70%;border-radius:0;"></div>
                <div class="tx-meta" style="margin-top:6px;">
                    <span style="background:rgba(17,16,16,.08);height:10px;width:80px;display:inline-block;"></span>
                </div>
            </div>
            <div class="tx-amt" style="background:rgba(17,16,16,.1);height:14px;width:70px;"></div>
        </div>
    @endfor
</div>
