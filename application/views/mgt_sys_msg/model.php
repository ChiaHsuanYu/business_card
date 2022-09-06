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

<!-- Confirm Modal -->
<input type="hidden" id="confirm_sys_msgId">
<input type="hidden" id="action">
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirm_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <input type="hidden" id="confirm_templateId">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirm_modal_label">系統訊息</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modal_hide('confirmModal');">&times;</button>
            </div>
            <div class="modal-body textCenter" id="confirm_model_body">新增後將發布訊息通知，是否確定新增?</div>
            <div class="btnBox">
                <button type="button" id="sys_msg_function" class="button width_60px" data-dismiss="modal" onclick="sys_msg_function();">確定</button>
                <button type="button" class="button width_60px" data-dismiss="modal" onclick="modal_hide('confirmModal');">取消</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="edit_confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirm_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <input type="hidden" id="confirm_templateId">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">系統訊息</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modal_hide('edit_confirmModal');">&times;</button>
            </div>
            <div class="modal-body textCenter">修改後將發布訊息通知，是否確定修改?</div>
            <div class="btnBox">
                <button type="button" class="button width_60px" data-dismiss="modal" onclick="edit_sys_msg()">確定</button>
                <button type="button" class="button width_60px" data-dismiss="modal" onclick="modal_hide('edit_confirmModal');">取消</button>
            </div>
        </div>
    </div>
</div>
