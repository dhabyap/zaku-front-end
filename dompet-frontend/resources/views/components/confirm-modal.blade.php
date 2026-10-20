<!-- Confirm Modal - Global -->
<div id="confirm-modal" style="
    display:none;
    position:fixed;inset:0;z-index:9999;
    background:rgba(17,16,16,.55);
    backdrop-filter:blur(2px);
    align-items:flex-end;
    justify-content:center;
">
    <div style="
        width:100%;max-width:430px;
        background:var(--paper,#FFFDF7);
        border-top:3px solid var(--ink,#111010);
        box-shadow:0 -8px 32px rgba(17,16,16,.15);
        padding:28px 20px 32px;
        transform:translateY(100%);
        transition:transform .25s cubic-bezier(.34,1.26,.64,1);
    " id="confirm-modal-inner">
        <div style="
            font-family:'DM Mono',monospace;
            font-size:9px;letter-spacing:3px;
            color:rgba(17,16,16,.4);margin-bottom:8px;
        ">// KONFIRMASI</div>
        <div id="confirm-modal-title" style="
            font-family:'Syne',sans-serif;
            font-size:22px;font-weight:800;
            color:var(--ink,#111010);
            line-height:1.15;margin-bottom:10px;
        ">Yakin?</div>
        <div id="confirm-modal-message" style="
            font-family:'DM Mono',monospace;
            font-size:12px;color:rgba(17,16,16,.55);
            line-height:1.6;margin-bottom:24px;
        "></div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <button id="confirm-modal-ok" style="
                font-family:'DM Mono',monospace;font-size:11px;
                font-weight:500;letter-spacing:2.5px;text-transform:uppercase;
                padding:14px 20px;border:2.5px solid var(--ink,#111010);
                background:var(--ink,#111010);color:var(--paper,#FFFDF7);
                cursor:pointer;box-shadow:4px 4px 0 rgba(17,16,16,.15);
                transition:transform .1s,box-shadow .1s;width:100%;
            " onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='4px 5px 0 rgba(17,16,16,.2)'"
               onmouseout="this.style.transform='';this.style.boxShadow='4px 4px 0 rgba(17,16,16,.15)'">
                HAPUS
            </button>
            <button id="confirm-modal-cancel" style="
                font-family:'DM Mono',monospace;font-size:11px;
                font-weight:500;letter-spacing:2.5px;text-transform:uppercase;
                padding:14px 20px;border:2.5px solid var(--ink,#111010);
                background:var(--paper,#FFFDF7);color:var(--ink,#111010);
                cursor:pointer;box-shadow:4px 4px 0 rgba(17,16,16,.1);
                transition:transform .1s,box-shadow .1s;width:100%;
            " onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='4px 5px 0 rgba(17,16,16,.15)'"
               onmouseout="this.style.transform='';this.style.boxShadow='4px 4px 0 rgba(17,16,16,.1)'">
                BATAL
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const overlay = document.getElementById('confirm-modal');
        const inner = document.getElementById('confirm-modal-inner');
        const cancelBtn = document.getElementById('confirm-modal-cancel');

        if (!overlay || !inner || !cancelBtn) return;

        window.__openConfirmModal = function () {
            overlay.style.display = 'flex';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    inner.style.transform = 'translateY(0)';
                });
            });
        };

        function closeModal() {
            inner.style.transform = 'translateY(100%)';
            setTimeout(() => { overlay.style.display = 'none'; }, 260);
        }

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                closeModal();
                if (window.__confirmResolve) { window.__confirmResolve(false); window.__confirmResolve = null; }
            }
        });

        document.getElementById('confirm-modal-ok').addEventListener('click', function () {
            closeModal();
            if (window.__confirmResolve) { window.__confirmResolve(true); window.__confirmResolve = null; }
        });

        cancelBtn.addEventListener('click', function () {
            closeModal();
            if (window.__confirmResolve) { window.__confirmResolve(false); window.__confirmResolve = null; }
        });
    })();
</script>
