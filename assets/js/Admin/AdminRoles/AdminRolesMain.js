/**
 * Admin Roles Management - Main JS
 * Handles On-Demand Personnel Selection, Checkbox Matrix, Assigned Users DataTable,
 * Role Summary DataTable, Add/Edit/Delete modals, and Swal2 operations.
 */
$(document).ready(function () {
    "use strict";

    // Ensure base_url is available
    if (typeof base_url === 'undefined') {
        var base_url = $('base').attr('href') || window.location.origin + '/';
    }

    // =============================================
    // 1. TABS — Tab switching
    // =============================================
    $(document).on('click', '[data-bs-tab]', function () {
        var tab = $(this).data('bs-tab');
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').removeClass('show active');
        $('#' + tab).addClass('show active');
    });

    // =============================================
    // 2. DATATABLES — Initialize tables
    // =============================================
    if ($.fn.DataTable) {
        if ($.fn.DataTable.isDataTable('#assignedPersonnelTable')) {
            $('#assignedPersonnelTable').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#roleSummaryTable')) {
            $('#roleSummaryTable').DataTable().destroy();
        }

        if ($('#assignedPersonnelTable').length) {
            $('#assignedPersonnelTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/th.json' },
                pageLength: 15,
                order: [[1, 'asc']],
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        }

        if ($('#roleSummaryTable').length) {
            $('#roleSummaryTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/th.json' },
                pageLength: 10,
                order: [[3, 'asc']],
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        }
    }

    // =============================================
    // 3. SELECT2 — Initialize Teacher Search Dropdown
    // =============================================
    if ($.fn.select2) {
        $('.select2-teacher').select2({
            dropdownParent: $('#modalPersonnelRoles'),
            placeholder: "-- พิมพ์ค้นหา หรือเลือกชื่อบุคลากร --",
            allowClear: true,
            width: '100%'
        });
    }

    // =============================================
    // 4. PERSONNEL MODAL: ADD / EDIT
    // =============================================

    // Open Modal for ADDING a new personnel
    window.openAddPersonnelModal = function () {
        $('#assignPersId').val('');
        $('#selectTeacherDropdown').val('').trigger('change');
        $('#sectionSelectTeacher').removeClass('d-none');
        $('#sectionDisplayTeacher').addClass('d-none');

        $('#modalPersonnelRolesTitle').html('<i class="bx bx-user-plus me-2"></i>เพิ่มผู้ใช้งานและกำหนดสิทธิ์');
        $('#modalPersonnelRolesSubtitle').text('เลือกบุคลากรและเลือกสิทธิ์การใช้งานที่ต้องการมอบหมาย');

        $('.system-chk').prop('checked', false);
        $('.role-checkbox-card').removeClass('is-active');

        $('#modalPersonnelRoles').modal('show');
    };

    // Click "แก้ไข" button on an assigned personnel row
    $(document).on('click', '.btn-edit-user-roles', function () {
        var persId   = $(this).data('pers-id');
        var fullname = $(this).data('fullname');
        var position = $(this).data('position');
        var systems  = $(this).data('systems');

        if (typeof systems === 'string') {
            try { systems = JSON.parse(systems); } catch(e) { systems = []; }
        }
        if (!Array.isArray(systems)) systems = [];

        $('#assignPersId').val(persId);
        $('#sectionSelectTeacher').addClass('d-none');
        $('#sectionDisplayTeacher').removeClass('d-none');
        $('#editTeacherInitial').text(fullname ? fullname.charAt(0) : 'U');
        $('#editTeacherName').text(fullname);
        $('#editTeacherMeta').text('รหัสประจำตัว: ' + persId + ' • ' + position);
        $('#modalPersonnelRolesTitle').html('<i class="bx bx-edit-alt me-2"></i>กำหนดระบบ: ' + fullname);
        $('#modalPersonnelRolesSubtitle').text('เลือกเฉพาะระบบที่บุคลากรท่านนี้สามารถทำงานได้');

        $('.system-chk').prop('checked', false);
        $('.role-checkbox-card').removeClass('is-active');
        systems.forEach(function (systemCode) {
            var $chk = $('#chk_system_' + systemCode);
            if ($chk.length) {
                $chk.prop('checked', true);
                $chk.closest('.role-checkbox-card').addClass('is-active');
            }
        });

        $('#modalPersonnelRoles').modal('show');
    });

    // Toggle card visual active state on checkbox change
    $(document).on('change', '.system-chk', function () {
        var $card = $(this).closest('.role-checkbox-card');
        if ($(this).is(':checked')) {
            $card.addClass('is-active');
        } else {
            $card.removeClass('is-active');
        }
    });

    // Select All Roles button
    $('#btnSelectAllSystems').on('click', function () {
        $('.system-chk').prop('checked', true);
        $('.role-checkbox-card').addClass('is-active');
    });

    // Clear All Roles button
    $('#btnClearAllSystems').on('click', function () {
        $('.system-chk').prop('checked', false);
        $('.role-checkbox-card').removeClass('is-active');
    });

    // Save Personnel Roles (Add or Edit)
    $('#formPersonnelRoles').on('submit', function (e) {
        e.preventDefault();

        var persId = $('#assignPersId').val();
        // If adding, get persId from dropdown
        if (!persId) {
            persId = $('#selectTeacherDropdown').val();
        }

        if (!persId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกบุคลากร',
                    text: 'โปรดค้นหาและเลือกชื่อบุคลากรที่ต้องการมอบหมายสิทธิ์'
                });
            } else {
                alert('กรุณาเลือกบุคลากร');
            }
            return;
        }

        var selectedSystems = [];
        $('.system-chk:checked').each(function () {
            selectedSystems.push($(this).val());
        });

        var $btn = $('#btnSavePersonnelRoles');
        $btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> กำลังบันทึก...');

        $.ajax({
            url: base_url + 'Admin/Rloes/savePersonnelRoles',
            type: 'POST',
            data: {
                pers_id: persId,
                systems: selectedSystems
            },
            dataType: 'json',
            success: function (res) {
                $('#modalPersonnelRoles').modal('hide');
                if (res.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสิทธิ์สำเร็จ',
                            text: res.message,
                            timer: 1400,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        alert(res.message || 'บันทึกสิทธิ์สำเร็จ');
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: res.message || 'ไม่สามารถบันทึกได้'
                        });
                    } else {
                        alert(res.message || 'ไม่สามารถบันทึกได้');
                    }
                }
            },
            error: function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                } else {
                    alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> บันทึกสิทธิ์');
            }
        });
    });

    // =============================================
    // 5. DELETE PERSONNEL ROLES (REVOKE ACCESS)
    // =============================================
    $(document).on('click', '.btn-delete-user-roles', function () {
        var persId   = $(this).data('pers-id');
        var fullname = $(this).data('fullname');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ยืนยันการลบสิทธิ์?',
                html: 'คุณต้องการยกเลิกสิทธิ์การใช้งานทั้งหมดของ <b>' + fullname + '</b> ใช่หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff3e1d',
                cancelButtonColor: '#8592a3',
                confirmButtonText: '<i class="bx bx-trash me-1"></i> ใช่, ลบสิทธิ์',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    executeDeletePersonnel(persId);
                }
            });
        } else {
            if (confirm('คุณต้องการยกเลิกสิทธิ์ทั้งหมดของ ' + fullname + ' ใช่หรือไม่?')) {
                executeDeletePersonnel(persId);
            }
        }
    });

    function executeDeletePersonnel(persId) {
        $.ajax({
            url: base_url + 'Admin/Rloes/deletePersonnelRoles/' + encodeURIComponent(persId),
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสิทธิ์สำเร็จ',
                            text: res.message,
                            timer: 1400,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        alert(res.message);
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: res.message
                        });
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                } else {
                    alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
                }
            }
        });
    }

    // =============================================
    // 6. MODAL: ADD / EDIT ROLE METADATA
    // =============================================
    window.openRoleModal = function () {
        resetRoleModal();
        $('#roleModalTitle').html('<i class="bx bx-shield-plus me-2"></i>เพิ่มประเภทสิทธิ์ใหม่');
        $('#role_status').prop('readonly', false);
        $('#roleModal').modal('show');
    };

    window.editRole = function (status, nanetype, level) {
        resetRoleModal();
        $('#role_id').val(status);
        $('#role_status').val(status).prop('readonly', true);
        $('#role_nanetype').val(nanetype);
        $('#role_level').val(level);
        $('#roleModalTitle').html('<i class="bx bx-edit-alt me-2"></i>แก้ไขประเภทสิทธิ์');
        $('#roleModal').modal('show');
    };

    function resetRoleModal() {
        $('#roleForm')[0].reset();
        $('#roleForm').removeClass('was-validated');
        $('#role_id').val('');
        $('#role_status').prop('readonly', false);
    }

    $('#roleForm').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        var $btn = $('#btnSaveRoleMeta');
        $btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> กำลังบันทึก...');

        var id = $('#role_id').val();
        var url = id
            ? base_url + 'Admin/Rloes/updateRole/' + encodeURIComponent(id)
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
                $('#roleModal').modal('hide');
                if (res.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            text: res.message,
                            timer: 1400,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        alert(res.message);
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: res.message
                        });
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                } else {
                    alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> บันทึก');
            }
        });
    });

    // =============================================
    // 7. MODAL: DELETE ROLE
    // =============================================
    var deleteRoleStatus = null;

    window.deleteRole = function (status, nanetype) {
        deleteRoleStatus = status;
        $('#deleteRoleName').text(nanetype + ' (' + status + ')');
        $('#deleteConfirmModal').modal('show');
    };

    $('#btnConfirmDelete').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> กำลังลบ...');

        $.ajax({
            url: base_url + 'Admin/Rloes/deleteRole/' + encodeURIComponent(deleteRoleStatus),
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                $('#deleteConfirmModal').modal('hide');
                if (res.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ',
                            text: res.message,
                            timer: 1400,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        alert(res.message);
                        window.location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: res.message
                        });
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function () {
                $('#deleteConfirmModal').modal('hide');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
                    });
                } else {
                    alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bx bx-trash me-1"></i> ลบ');
                deleteRoleStatus = null;
            }
        });
    });

    $('#deleteConfirmModal').on('hidden.bs.modal', function () {
        deleteRoleStatus = null;
    });
});



