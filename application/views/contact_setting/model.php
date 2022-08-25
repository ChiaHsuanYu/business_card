<!-- msg Modal -->
<div class="modal fade" id="msgModal" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_label">-</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modal_hide('msgModal');">&times;</button>
            </div>
            <div class="modal-body textCenter" id="model_body"></div>
            <div class="btnBox">
                <button type="button" class="button width_60px" data-dismiss="modal" onclick="modal_hide('msgModal');">確定</button>
            </div>
        </div>
    </div>
</div>

<!-- confirm Modal -->
<div class="modal fade" id="contactSettingModal" tabindex="-1" role="dialog" aria-labelledby="contact_setting_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contact_setting_modal_label">系統訊息</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modal_hide('contactSettingModal');">&times;</button>
            </div>
            <div class="modal-body textCenter" id="contact_setting_model_body">是否確定修改親密度累積設定?</div>
            <div class="btnBox">
                <button type="button" class="button width_60px" data-dismiss="modal" onclick="update_contact_setting()">確定</button>
                <button type="button" class="button width_60px" data-dismiss="modal" onclick="modal_hide('contactSettingModal');">取消</button>
            </div>
        </div>
    </div>
</div>