/**
 * Admin Roles Management - Main JS
 * Handles DataTable, Add/Edit/Delete modals, Select2, and AJAX operations.
 * (No SweetAlert2 - uses Bootstrap Modal + custom toast)
 */
$(document).ready(function () {
    "use strict";

    // Ensure base_url is available
    if (typeof base_url === 'undefined') {
        var base_url = $('base').attr('href') || window.location.origin + '/';
    }

    // =============================================
    // TOAST HELPER - Custom toast notification
    // =============================================
    function showToast(message, type) {
        type = type || 'success';
        var bgClass = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-warning');
        var icon = type === 'success' ? 'bx-check-circle' : (type === 'error' ? 'bx-error-circle' : 'bx-info-circle');

        var $toast = $(
            '<div class="toast align-items-center ' + bgClass + ' text-white border-0 position-fixed" role="alert" ' +
            'style="top: 20px; right: 20px; z-index: 1080; min-width: 280px; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.15);">' +
            '  <div class="d-flex">' +
            '    <div class="toast-body fw-semibold"><i class="bx ' + icon + ' me-2"></i>' + message + '</div>' +
            '    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '  </div>' +
            '</div>'
        );

        $('body').append($toast);
        var bsToast = new bootstrap.Toast($toast[0], { delay: 2500 });
        bsToast.show();
        $toast.on('hidden.bs.toast', function () { $toast.remove(); });
    }

    // =============================================
    // 1. TABS — Simple tab switching
    // =============================================
    $(document).on('click', '[data-bs-tab]', function () {
        var tab = $(this).data('bs-tab');
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').removeClass('show active');
        $('#' + tab).addClass('show active');
    });

    // =============================================
    // 2. STATS — Calculate and display role statistics
    // =============================================
    function updateStats() {
        var total = 0, assigned = 0, unassigned = 0;
        $('#roleTable tbody tr').each(function () {
            total++;
            var userCell = $(this).find('td:eq(4)').text();
            if (userCell.indexOf('ยังไม่กำหนด') !== -1) {
                unassigned++;
            } else {
                assigned++;
            }
        });
        $('#totalRoles').text(total);
        $('#totalAssigned').text(assigned);
        $('#totalUnassigned').text(unassigned);
    }
    updateStats();

    // =============================================
    // 3. DATATABLES — Initialize role tables
    // =============================================
    if ($.fn.DataTable) {
        var dtConfig = {
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/th.json' },
            pageLength: 10,
            order: [[3, 'asc']],
            columnDefs: [{ orderable: false, targets: -1 }]
        };
        if ($.fn.DataTable.isDataTable('#roleTable')) {
            $('#roleTable').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#assignmentTable')) {
            $('#assignmentTable').DataTable().destroy();
        }
        $('#roleTable').DataTable(dtConfig);
        $('#assignmentTable').DataTable(dtConfig);
    }

    // =============================================
    // 4. SELECT2 — Initialize personnel selectors
    // =============================================
    $('.select2-assignment').select2({
        placeholder: "เลือกเจ้าหน้าที่",
        allowClear: true,
        width: 'resolve'
    });

    // =============================================
    // 5. MODAL — Add/Edit Role
    // =============================================
    window.openRoleModal = function () {
        resetRoleModal();
        $('#roleModalTitle').html('<i class="bx bx-shield-plus me-2"></i>เพิ่มสิทธิ์');
        $('#roleModal').modal('show');
    };

    window.editRole = function (id, status, nanetype, level) {
        resetRoleModal();
        $('#role_id').val(id);
        $('#role_status').val(status);
        $('#role_nanetype').val(nanetype);
        $('#role_level').val(level);
        $('#roleModalTitle').html('<i class="bx bx-edit-alt me-2"></i>แก้ไขสิทธิ์');
        $('#roleModal').modal('show');
    };

    function resetRoleModal() {
        $('#roleForm')[0].reset();
        $('#roleForm').removeClass('was-validated');
        $('#role_id').val('');
    }

    // Save role (add or edit)
    $('#roleForm').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> กำลังบันทึก...');

        var id = $('#role_id').val();
        var url = id
            ? base_url + 'Admin/Rloes/updateRole/' + id
            : base_url + 'Admin/Rloes/addRole';
        var formData = {
            status:   $('#role_status').val(),
            nanetype: $('#role_nanetype').val(),
            level:    $('#role_level').val()
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    showToast(res.message || 'บันทึกสำเร็จ', 'success');
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else {
                    showToast(res.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function () {
                showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            },
            complete: function () {
                $submitBtn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> บันทึก');
            }
        });
    });

    // =============================================
    // 6. DELETE ROLE — Uses Bootstrap Modal
    // =============================================
    var deleteRoleId = null;

    window.deleteRole = function (id, nanetype) {
        deleteRoleId = id;
        $('#deleteRoleName').text(nanetype);
        $('#deleteConfirmModal').modal('show');
    };

    $('#btnConfirmDelete').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> กำลังลบ...');

        $.ajax({
            url: base_url + 'Admin/Rloes/deleteRole/' + deleteRoleId,
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                $('#deleteConfirmModal').modal('hide');
                if (res.status === 'success') {
                    showToast(res.message || 'ลบสำเร็จ', 'success');
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else {
                    showToast(res.message || 'ไม่สามารถลบได้', 'error');
                }
            },
            error: function () {
                $('#deleteConfirmModal').modal('hide');
                showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bx bx-trash me-1"></i> ลบ');
                deleteRoleId = null;
            }
        });
    });

    // Reset delete modal state on close
    $('#deleteConfirmModal').on('hidden.bs.modal', function () {
        deleteRoleId = null;
    });

    // =============================================
    // 7. SAVE PERSONNEL ASSIGNMENT (per row)
    // =============================================
    window.saveAssignment = function (btn) {
        var $btn = $(btn);
        var $row = $btn.closest('tr');
        var $select = $row.find('.select2-assignment');
        var teachID  = $select.val();
        var rloesID  = $select.data('rloes-id');
        var keytype  = $select.data('rloes-nanetype');

        if (!teachID) {
            showToast('กรุณาเลือกผู้ใช้งาน', 'warning');
            return;
        }

        $btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> กำลังบันทึก...');

        $.post(base_url + 'Admin/Rloes/RloesSettingManager', {
            TeachID: teachID,
            RloesID: rloesID,
            Keytype: keytype
        }, function (data) {
            if (data.status === 'success' || data == 1) {
                showToast('เป็นผู้ใช้งานเรียบร้อย', 'success');
            } else {
                showToast('เปลี่ยนแปลงข้อมูลไม่สำเร็จ', 'error');
            }
            $btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> บันทึก');
        }).fail(function () {
            showToast('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
            $btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> บันทึก');
        });
    };
});
