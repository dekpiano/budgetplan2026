/**
 * Admin Budget Ordinance - Registration Fee Ledger JS
 * Features:
 *  - Select2 with multi-tags and dropdownParent inside Bootstrap Modals
 *  - Thai Buddhist Calendar (Flatpickr พ.ศ.) on all date inputs
 *  - Dynamic Fiscal Year Calculation (1 Oct - 30 Sep)
 *  - Auto Training Days calculation
 *  - Real-time Expense Calculation: Fee Per Person * Attendee Count = Total Withdraw
 *  - Currency Inputmask
 *  - Budget Allocation check before withdraw
 *  - AJAX Save / Edit / Delete with SweetAlert2
 */

var thaiMonthsFull = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
];

var fpRegistrationDocDate = null;
var fpRegistrationStartDate = null;
var fpRegistrationEndDate = null;
var fpBudgetDocDate = null;

// Helper: Format Date for Display in Thai Buddhist Era (พ.ศ. เช่น 28 ส.ค. 2569)
function formatThaiBuddhistDateDisplay(dateObj) {
    if (!dateObj) return '';
    var d = (dateObj instanceof Date) ? dateObj : new Date(dateObj);
    if (isNaN(d.getTime())) return '';
    var day = d.getDate();
    var month = thaiMonthsFull[d.getMonth()];
    var year = d.getFullYear();
    if (year < 2400) {
        year += 543;
    }
    return day + ' ' + month + ' ' + year;
}

// Helper: Calculate Thai Fiscal Budget Year from Date (1 Oct - 30 Sep)
function calculateBudgetYearFromDate(dateObj) {
    if (!dateObj) return '';
    var d = (dateObj instanceof Date) ? dateObj : new Date(dateObj);
    if (isNaN(d.getTime())) return '';
    var year = d.getFullYear();
    var month = d.getMonth(); // 0 = Jan ... 9 = Oct, 10 = Nov, 11 = Dec
    if (year < 2400) {
        year += 543;
    }
    // เดือน 10 (ตุลาคม) เป็นต้นไป นับเป็นปีงบประมาณถัดไป
    if (month >= 9) {
        year += 1;
    }
    return String(year);
}

// Update Registration Modal Budget Year Display & Hidden Value
function updateRegistrationModalBudgetYear(dateObj) {
    if (!dateObj) return;
    var fiscalYear = calculateBudgetYearFromDate(dateObj);
    if (fiscalYear) {
        $('#registration_budget_year').val(fiscalYear);
        $('#registrationModalYearDisplay').html('ปีงบประมาณ พ.ศ. ' + fiscalYear + ' <span class="badge bg-white text-dark ms-1 fs-9 shadow-xs">อัตโนมัติ</span>');
    }
}

// Update Budget Adjust Modal Year Display & Hidden Value
function updateBudgetAdjustRegistrationBudgetYear(dateObj) {
    if (!dateObj) return;
    var fiscalYear = calculateBudgetYearFromDate(dateObj);
    if (fiscalYear) {
        $('#budget_adjust_registration_year').val(fiscalYear);
        $('#budgetAdjustRegistrationYearDisplay').html('ปีงบประมาณ พ.ศ. ' + fiscalYear + ' <span class="badge bg-white text-dark ms-1 fs-9 shadow-xs">อัตโนมัติ</span>');
        $('#budget_adjust_registration_description').val('จัดสรรงบประมาณค่าลงทะเบียนอบรม/สัมมนา ประจำปีงบประมาณ พ.ศ. ' + fiscalYear);
    }
}

// Helper: Update Calendar Year Header in Flatpickr popup to Buddhist Era (พ.ศ.)
function updateBuddhistYearInCalendar(instance) {
    setTimeout(function () {
        if (instance && instance.currentYearElement) {
            var y = parseInt(instance.currentYearElement.value);
            if (y < 2400) {
                instance.currentYearElement.value = y + 543;
            }
        }
    }, 15);
}

// Helper: Calculate Training Days difference accurately
function calculateTrainingDays() {
    var d1 = null;
    var d2 = null;

    if (fpRegistrationStartDate && fpRegistrationStartDate.selectedDates.length > 0) {
        d1 = fpRegistrationStartDate.selectedDates[0];
    } else {
        var rawStart = $('#registration_start_date').val();
        if (rawStart) d1 = new Date(rawStart);
    }

    if (fpRegistrationEndDate && fpRegistrationEndDate.selectedDates.length > 0) {
        d2 = fpRegistrationEndDate.selectedDates[0];
    } else {
        var rawEnd = $('#registration_end_date').val();
        if (rawEnd) d2 = new Date(rawEnd);
    }

    if (!d1) return;

    // If end date is not set, default to start date (1 day training)
    if (!d2) {
        if (fpRegistrationEndDate) {
            fpRegistrationEndDate.setDate(d1, true);
        }
        $('#registration_training_days').val(1);
        return;
    }

    var utc1 = Date.UTC(d1.getFullYear(), d1.getMonth(), d1.getDate());
    var utc2 = Date.UTC(d2.getFullYear(), d2.getMonth(), d2.getDate());

    if (utc2 < utc1) {
        if (fpRegistrationEndDate) {
            fpRegistrationEndDate.setDate(d1, true);
        }
        $('#registration_training_days').val(1);
    } else {
        var diffTime = utc2 - utc1;
        var diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
        $('#registration_training_days').val(diffDays >= 1 ? diffDays : 1);
    }
}

// Helper: Live calculate total withdraw: fee_per_person * attendee_count
function calculateTotalRegistrationAmount() {
    var parseVal = function (id) {
        var raw = $(id).val() || '0';
        var clean = raw.replace(/,/g, '');
        return parseFloat(clean) || 0;
    };

    var fee = parseVal('#registration_fee_per_person');
    var count = parseInt($('#registration_attendee_count').val()) || 1;

    if (fee > 0 && count > 0) {
        var total = (fee * count).toFixed(2);
        $('#registration_amount_withdraw').val(total);
    }
}

$(document).ready(function () {
    // ----------------------------------------------------
    // 1. Initialize DataTable (Only if data rows exist)
    // ----------------------------------------------------
    if ($('#tableRegistrationLedger').length) {
        if ($('#tableRegistrationLedger tbody td[colspan]').length === 0 && $('#tableRegistrationLedger tbody tr').length > 0) {
            $('#tableRegistrationLedger').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
                },
                ordering: false,
                pageLength: 25,
                responsive: false,
                autoWidth: false,
                scrollX: false
            });
        }
    }

    // ----------------------------------------------------
    // 2. Initialize Thai Buddhist Calendar (Flatpickr พ.ศ.)
    // ----------------------------------------------------
    function initBuddhistFlatpickr() {
        if (typeof flatpickr !== 'undefined') {
            if (flatpickr.l10ns && flatpickr.l10ns.th) {
                flatpickr.localize(flatpickr.l10ns.th);
            }

            $('.flatpickr-thai-buddhist').each(function () {
                var $el = $(this);
                var elementId = $el.attr('id');
                var isDocDate = ($el.attr('name') === 'doc_date');
                var fp = $el.flatpickr({
                    locale: 'th',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'Y-m-d',
                    altInputClass: 'form-control border-start-0 bg-white',
                    defaultDate: isDocDate ? 'today' : null,
                    onChange: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0 && instance.altInput) {
                            instance.altInput.value = formatThaiBuddhistDateDisplay(selectedDates[0]);
                            if (elementId === 'registration_doc_date') {
                                updateRegistrationModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_registration_doc_date') {
                                updateBudgetAdjustRegistrationBudgetYear(selectedDates[0]);
                            }
                        }
                        if (elementId === 'registration_start_date' || elementId === 'registration_end_date') {
                            setTimeout(calculateTrainingDays, 20);
                        }
                    },
                    onReady: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0 && instance.altInput) {
                            instance.altInput.value = formatThaiBuddhistDateDisplay(selectedDates[0]);
                            if (elementId === 'registration_doc_date') {
                                updateRegistrationModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_registration_doc_date') {
                                updateBudgetAdjustRegistrationBudgetYear(selectedDates[0]);
                            }
                        }
                        updateBuddhistYearInCalendar(instance);
                    },
                    onMonthChange: function (selectedDates, dateStr, instance) {
                        updateBuddhistYearInCalendar(instance);
                    },
                    onYearChange: function (selectedDates, dateStr, instance) {
                        updateBuddhistYearInCalendar(instance);
                    },
                    onOpen: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0 && instance.altInput) {
                            instance.altInput.value = formatThaiBuddhistDateDisplay(selectedDates[0]);
                        }
                        updateBuddhistYearInCalendar(instance);
                    },
                    onClose: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0 && instance.altInput) {
                            instance.altInput.value = formatThaiBuddhistDateDisplay(selectedDates[0]);
                            if (elementId === 'registration_doc_date') {
                                updateRegistrationModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_registration_doc_date') {
                                updateBudgetAdjustRegistrationBudgetYear(selectedDates[0]);
                            }
                        }
                    }
                });

                if (elementId === 'registration_doc_date') {
                    fpRegistrationDocDate = fp;
                } else if (elementId === 'registration_start_date') {
                    fpRegistrationStartDate = fp;
                } else if (elementId === 'registration_end_date') {
                    fpRegistrationEndDate = fp;
                } else if (elementId === 'budget_adjust_registration_doc_date') {
                    fpBudgetDocDate = fp;
                }
            });
        }
    }
    initBuddhistFlatpickr();

    // ----------------------------------------------------
    // Auto-update end date when manually changing days
    // ----------------------------------------------------
    $('#registration_training_days').on('input change', function () {
        var days = parseInt($(this).val()) || 1;
        if (days < 1) days = 1;
        if (fpRegistrationStartDate && fpRegistrationStartDate.selectedDates.length > 0) {
            var d1 = fpRegistrationStartDate.selectedDates[0];
            var d2 = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate() + (days - 1));
            if (fpRegistrationEndDate) {
                fpRegistrationEndDate.setDate(d2, true);
            }
        }
    });

    // ----------------------------------------------------
    // 3. Initialize Select2 (inside Modal)
    // ----------------------------------------------------
    function initSelect2() {
        if ($.fn.select2) {
            $('#select_registration_requester').select2({
                dropdownParent: $('#modalAddRegistration'),
                placeholder: '-- ค้นหา / เลือกผู้ขอ หรือพิมพ์ชื่อใหม่ --',
                allowClear: true,
                tags: true,
                width: '100%'
            });

            $('#select_registration_attendees').select2({
                dropdownParent: $('#modalAddRegistration'),
                placeholder: '-- ค้นหา / เลือกผู้เข้าร่วม หรือพิมพ์ชื่อใหม่ --',
                allowClear: true,
                tags: true,
                width: '100%'
            });
        }
    }

    $('#modalAddRegistration').on('shown.bs.modal', function () {
        initSelect2();
    });

    // Attendees change event: auto update count and hidden input
    $('#select_registration_attendees').on('change', function () {
        var selected = $(this).val() || [];
        $('#registration_attendee_names').val(selected.join(', '));
        if (selected.length > 0) {
            $('#registration_attendee_count').val(selected.length);
        }
        calculateTotalRegistrationAmount();
    });

    // ----------------------------------------------------
    // Handle Click on Add Registration Button (Budget Check)
    // ----------------------------------------------------
    $(document).on('click', '#btnOpenAddRegistration', function (e) {
        var hasBudget = $(this).data('has-budget');
        var year = $(this).data('year') || '';
        if (hasBudget === 0 || hasBudget === '0') {
            e.preventDefault();
            e.stopPropagation();
            Swal.fire({
                icon: 'warning',
                title: 'ยังไม่ได้ตั้งงบประมาณ',
                html: 'ยังไม่มีการตั้งงบประมาณสำหรับปีงบประมาณ พ.ศ. <b>' + year + '</b><br><span class="text-muted fs-8">กรุณากดปุ่ม <b>"ตั้งงบประมาณ"</b> ก่อนเริ่มบันทึกรายการเบิกจ่าย</span>',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-plus-circle me-1"></i> ตั้งงบประมาณตอนนี้',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#FB8C00',
                cancelButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-primary rounded-pill px-4 btn-orange-main shadow-sm',
                    cancelButton: 'btn btn-outline-secondary rounded-pill px-4'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#modalAddBudget').modal('show');
                }
            });
            return false;
        }
        $('#modalAddRegistration').modal('show');
    });

    // ----------------------------------------------------
    // 4. Initialize Inputmask for money inputs
    // ----------------------------------------------------
    if ($.fn.inputmask) {
        $('.input-money').inputmask('currency', {
            prefix: '',
            groupSeparator: ',',
            alias: 'numeric',
            placeholder: '0',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            clearMaskOnLostFocus: false,
            rightAlign: true
        });
    }

    // ----------------------------------------------------
    // 5. Live Calculation: fee_per_person * attendee_count
    // ----------------------------------------------------
    $('#registration_fee_per_person, #registration_attendee_count').on('input change keyup', function () {
        calculateTotalRegistrationAmount();
    });

    // Helper: Button Loading State
    function setBtnLoading($btn, isLoading, defaultText) {
        if (isLoading) {
            $btn.prop('disabled', true);
            $btn.find('.spinner-border').removeClass('d-none');
            $btn.find('.btn-icon').addClass('d-none');
            $btn.find('.btn-text').text('กำลังบันทึก...');
        } else {
            $btn.prop('disabled', false);
            $btn.find('.spinner-border').addClass('d-none');
            $btn.find('.btn-icon').removeClass('d-none');
            $btn.find('.btn-text').text(defaultText);
        }
    }

    // ----------------------------------------------------
    // 6. Submit Form: บันทึกค่าลงทะเบียน (Withdraw)
    // ----------------------------------------------------
    $('#formSaveRegistration').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnSubmitRegistration');

        setBtnLoading($btn, true, 'บันทึกข้อมูล');

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Registration/Save',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (res) {
                setBtnLoading($btn, false, 'บันทึกข้อมูล');
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: res.message || 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            },
            error: function () {
                setBtnLoading($btn, false, 'บันทึกข้อมูล');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                });
            }
        });
    });

    // ----------------------------------------------------
    // 7. Submit Form: ตั้งงบประมาณ / ปรับยอดงบประมาณ (Adjust)
    // ----------------------------------------------------
    $('#formSaveBudgetAdjust').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnSubmitBudget');

        setBtnLoading($btn, true, 'บันทึกงบประมาณ');

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Registration/Save',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (res) {
                setBtnLoading($btn, false, 'บันทึกงบประมาณ');
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: res.message || 'ไม่สามารถบันทึกงบประมาณได้'
                    });
                }
            },
            error: function () {
                setBtnLoading($btn, false, 'บันทึกงบประมาณ');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถบันทึกข้อมูลได้'
                });
            }
        });
    });

    // ----------------------------------------------------
    // 8. Edit Item (Fetch via AJAX and fill modal)
    // ----------------------------------------------------
    $(document).on('click', '.btn-edit-registration', function () {
        var id = $(this).data('id');
        if (!id) return;

        Swal.fire({
            title: 'กำลังโหลดข้อมูล...',
            allowOutsideClick: false,
            didOpen: function () {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Registration/getRegistrationData/' + id,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                Swal.close();
                if (res.status === 'success' && res.data) {
                    var d = res.data;
                    $('#registration_id').val(d.id);

                    if (fpRegistrationDocDate && d.doc_date) {
                        fpRegistrationDocDate.setDate(d.doc_date, true);
                    } else {
                        $('#registration_doc_date').val(d.doc_date || '');
                    }

                    $('#registration_dika_no').val(d.dika_no || '');
                    $('#registration_doc_no').val(d.doc_no || '');
                    $('#registration_description').val(d.description || '');
                    $('#registration_organizer').val(d.organizer || '');
                    $('#registration_location').val(d.location || '');

                    if (fpRegistrationStartDate && d.training_start_date) {
                        fpRegistrationStartDate.setDate(d.training_start_date, true);
                    } else {
                        $('#registration_start_date').val(d.training_start_date || '');
                    }

                    if (fpRegistrationEndDate && d.training_end_date) {
                        fpRegistrationEndDate.setDate(d.training_end_date, true);
                    } else {
                        $('#registration_end_date').val(d.training_end_date || '');
                    }

                    $('#registration_training_days').val(d.training_days || 1);
                    $('#registration_attendee_count').val(d.attendee_count || 1);
                    $('#registration_fee_per_person').val(d.fee_per_person > 0 ? d.fee_per_person : '');
                    $('#registration_amount_withdraw').val(d.amount_withdraw > 0 ? d.amount_withdraw : '');
                    $('#registration_receipt_no').val(d.receipt_no || '');
                    $('#registration_notes').val(d.notes || '');

                    initSelect2();

                    // Set Requester Name
                    if (d.requester_name) {
                        if ($('#select_registration_requester').find("option[value='" + d.requester_name + "']").length) {
                            $('#select_registration_requester').val(d.requester_name).trigger('change');
                        } else {
                            var newReqOpt = new Option(d.requester_name, d.requester_name, true, true);
                            $('#select_registration_requester').append(newReqOpt).trigger('change');
                        }
                    } else {
                        $('#select_registration_requester').val('').trigger('change');
                    }

                    // Set Attendees Multi-tags
                    if (d.attendee_names) {
                        var names = d.attendee_names.split(',').map(function (item) { return item.trim(); }).filter(Boolean);
                        names.forEach(function (name) {
                            if (!$('#select_registration_attendees').find("option[value='" + name + "']").length) {
                                var newAttOpt = new Option(name, name, true, true);
                                $('#select_registration_attendees').append(newAttOpt);
                            }
                        });
                        $('#select_registration_attendees').val(names).trigger('change');
                    } else {
                        $('#select_registration_attendees').val([]).trigger('change');
                    }

                    $('#modalRegistrationTitle').html('แก้ไขรายการเบิกจ่ายค่าลงทะเบียน');
                    $('#modalAddRegistration').modal('show');
                } else {
                    Swal.fire('ผิดพลาด', res.message || 'ไม่พบข้อมูล', 'error');
                }
            },
            error: function () {
                Swal.close();
                Swal.fire('ผิดพลาด', 'ไม่สามารถดึงข้อมูลได้', 'error');
            }
        });
    });

    // ----------------------------------------------------
    // 9. Reset Modal on Close
    // ----------------------------------------------------
    $('#modalAddRegistration').on('hidden.bs.modal', function () {
        $('#formSaveRegistration')[0].reset();
        $('#registration_id').val('');
        $('#select_registration_requester').val('').trigger('change');
        $('#select_registration_attendees').val([]).trigger('change');
        $('#modalRegistrationTitle').html('บันทึกรายการเบิกจ่ายค่าลงทะเบียนอบรม/สัมมนา');
        if (fpRegistrationDocDate) {
            fpRegistrationDocDate.setDate('today', true);
        }
        if (fpRegistrationStartDate) {
            fpRegistrationStartDate.clear();
        }
        if (fpRegistrationEndDate) {
            fpRegistrationEndDate.clear();
        }
    });

    // ----------------------------------------------------
    // 10. Delete Item
    // ----------------------------------------------------
    $(document).on('click', '.btn-delete-registration', function () {
        var id = $(this).data('id');
        var desc = $(this).data('desc') || 'รายการนี้';

        Swal.fire({
            title: 'ยืนยันการลบรายการ?',
            html: 'คุณต้องการลบรายการ <b>"' + desc + '"</b> หรือไม่?<br><span class="text-danger fs-8">การลบนี้จะส่งผลต่อยอดเงินคงเหลือสะสม</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bx bx-trash me-1"></i> ลบข้อมูล',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#e53935',
            cancelButtonColor: '#6c757d',
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4 shadow-sm',
                cancelButton: 'btn btn-outline-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบข้อมูล...',
                    allowOutsideClick: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: window.appBaseUrl + 'Admin/BudgetOrdinance/Registration/Delete/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function () {
                                location.reload();
                            });
                        } else {
                            Swal.fire('ผิดพลาด', res.message || 'ไม่สามารถลบข้อมูลได้', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                    }
                });
            }
        });
    });
});
