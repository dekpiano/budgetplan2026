/**
 * Admin Budget Ordinance - Travel Expenses JS
 * Features:
 *  - Select2 with tags and dropdownParent inside Bootstrap Modals
 *  - Thai Buddhist Calendar (Flatpickr พ.ศ.) on all date inputs
 *  - High-precision Travel Days & Date Range Calculation
 *  - Real-time Expense Breakdown Calculation: Allowance + Accommodation + Transport + Other = Total Withdraw
 *  - Currency Inputmask
 *  - AJAX Save / Edit / Delete with SweetAlert2
 */

var thaiMonthsFull = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
];

var fpTravelDocDate = null;
var fpTravelStartDate = null;
var fpTravelEndDate = null;
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

// Update Travel Modal Budget Year Display & Hidden Value
function updateTravelModalBudgetYear(dateObj) {
    if (!dateObj) return;
    var fiscalYear = calculateBudgetYearFromDate(dateObj);
    if (fiscalYear) {
        $('#travel_budget_year').val(fiscalYear);
        $('#travelModalYearDisplay').html('ปีงบประมาณ พ.ศ. ' + fiscalYear + ' <span class="badge bg-white text-dark ms-1 fs-9 shadow-xs">อัตโนมัติ</span>');
    }
}

// Update Budget Adjust Modal Year Display & Hidden Value
function updateBudgetAdjustTravelBudgetYear(dateObj) {
    if (!dateObj) return;
    var fiscalYear = calculateBudgetYearFromDate(dateObj);
    if (fiscalYear) {
        $('#budget_adjust_travel_year').val(fiscalYear);
        $('#budgetAdjustTravelYearDisplay').html('ปีงบประมาณ พ.ศ. ' + fiscalYear + ' <span class="badge bg-white text-dark ms-1 fs-9 shadow-xs">อัตโนมัติ</span>');
        $('#budget_adjust_travel_description').val('จัดสรรงบประมาณค่าใช้จ่ายเดินทางไปราชการ ประจำปีงบประมาณ พ.ศ. ' + fiscalYear);
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

// Helper: Calculate Travel Days difference accurately
function calculateTravelDays() {
    var d1 = null;
    var d2 = null;

    if (fpTravelStartDate && fpTravelStartDate.selectedDates.length > 0) {
        d1 = fpTravelStartDate.selectedDates[0];
    } else {
        var rawStart = $('#travel_start_date').val();
        if (rawStart) d1 = new Date(rawStart);
    }

    if (fpTravelEndDate && fpTravelEndDate.selectedDates.length > 0) {
        d2 = fpTravelEndDate.selectedDates[0];
    } else {
        var rawEnd = $('#travel_end_date').val();
        if (rawEnd) d2 = new Date(rawEnd);
    }

    if (!d1) return;

    // If end date is not set, default to start date (1 day trip)
    if (!d2) {
        if (fpTravelEndDate) {
            fpTravelEndDate.setDate(d1, true);
        }
        $('#travel_days').val(1);
        return;
    }

    // Compute difference using UTC midnight timestamps
    var utc1 = Date.UTC(d1.getFullYear(), d1.getMonth(), d1.getDate());
    var utc2 = Date.UTC(d2.getFullYear(), d2.getMonth(), d2.getDate());

    if (utc2 < utc1) {
        // If end date is before start date, auto-sync end date to start date
        if (fpTravelEndDate) {
            fpTravelEndDate.setDate(d1, true);
        }
        $('#travel_days').val(1);
    } else {
        var diffTime = utc2 - utc1;
        var diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
        $('#travel_days').val(diffDays >= 1 ? diffDays : 1);
    }
}

$(document).ready(function () {
    // ----------------------------------------------------
    // 1. Initialize DataTable (Only if data rows exist)
    // ----------------------------------------------------
    if ($('#tableTravelLedger').length) {
        if ($('#tableTravelLedger tbody td[colspan]').length === 0 && $('#tableTravelLedger tbody tr').length > 0) {
            $('#tableTravelLedger').DataTable({
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
                            if (elementId === 'travel_doc_date') {
                                updateTravelModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_travel_doc_date') {
                                updateBudgetAdjustTravelBudgetYear(selectedDates[0]);
                            }
                        }
                        if (elementId === 'travel_start_date' || elementId === 'travel_end_date') {
                            setTimeout(calculateTravelDays, 20);
                        }
                    },
                    onReady: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0 && instance.altInput) {
                            instance.altInput.value = formatThaiBuddhistDateDisplay(selectedDates[0]);
                            if (elementId === 'travel_doc_date') {
                                updateTravelModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_travel_doc_date') {
                                updateBudgetAdjustTravelBudgetYear(selectedDates[0]);
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
                            if (elementId === 'travel_doc_date') {
                                updateTravelModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_travel_doc_date') {
                                updateBudgetAdjustTravelBudgetYear(selectedDates[0]);
                            }
                        }
                    }
                });

                if (elementId === 'travel_doc_date') {
                    fpTravelDocDate = fp;
                } else if (elementId === 'travel_start_date') {
                    fpTravelStartDate = fp;
                } else if (elementId === 'travel_end_date') {
                    fpTravelEndDate = fp;
                } else if (elementId === 'budget_adjust_travel_doc_date') {
                    fpBudgetDocDate = fp;
                }
            });
        }
    }
    initBuddhistFlatpickr();

    // ----------------------------------------------------
    // Auto-update end date when manually changing days
    // ----------------------------------------------------
    $('#travel_days').on('input change', function () {
        var days = parseInt($(this).val()) || 1;
        if (days < 1) days = 1;
        if (fpTravelStartDate && fpTravelStartDate.selectedDates.length > 0) {
            var d1 = fpTravelStartDate.selectedDates[0];
            var d2 = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate() + (days - 1));
            if (fpTravelEndDate) {
                fpTravelEndDate.setDate(d2, true);
            }
        }
    });

    // ----------------------------------------------------
    // 3. Initialize Select2 (inside Modal)
    // ----------------------------------------------------
    function initSelect2() {
        if ($.fn.select2) {
            $('#select_travel_requester').select2({
                dropdownParent: $('#modalAddTravel'),
                placeholder: '-- ค้นหา / เลือกผู้ขอ หรือพิมพ์ชื่อใหม่ --',
                allowClear: true,
                tags: true,
                width: '100%'
            });
        }
    }

    $('#modalAddTravel').on('shown.bs.modal', function () {
        initSelect2();
    });

    // Handle Click on Add Travel Button (Check if budget is allocated)
    $(document).on('click', '#btnOpenAddTravel', function (e) {
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
        $('#modalAddTravel').modal('show');
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
    // 5. Live Calculation: Sum of Expense Breakdown
    // ----------------------------------------------------
    function calculateTotalTravelAmount() {
        var parseVal = function (id) {
            var raw = $(id).val() || '0';
            var clean = raw.replace(/,/g, '');
            return parseFloat(clean) || 0;
        };

        var allowance = parseVal('#travel_allowance_amount');
        var accommodation = parseVal('#travel_accommodation_amount');
        var transport = parseVal('#travel_transport_amount');
        var other = parseVal('#travel_other_expenses_amount');

        var total = allowance + accommodation + transport + other;
        if (total > 0) {
            $('#travel_amount_withdraw').val(total.toFixed(2));
        }
    }

    $('.travel-calc-field').on('input keyup change', function () {
        calculateTotalTravelAmount();
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
    // 6. Submit Form: บันทึกค่าใช้จ่ายเดินทางไปราชการ (Withdraw)
    // ----------------------------------------------------
    $('#formSaveTravel').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnSubmitTravel');

        setBtnLoading($btn, true, 'บันทึกข้อมูล');

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Travel/Save',
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
                        title: 'ผิดพลาด',
                        text: res.message || 'ไม่สามารถบันทึกข้อมูลได้'
                    });
                }
            },
            error: function () {
                setBtnLoading($btn, false, 'บันทึกข้อมูล');
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ',
                    text: 'กรุณาลองใหม่อีกครั้ง'
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
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Travel/Save',
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
    $(document).on('click', '.btn-edit-travel', function () {
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
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Travel/getTravelData/' + id,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                Swal.close();
                if (res.status === 'success' && res.data) {
                    var d = res.data;
                    $('#travel_id').val(d.id);

                    // Set Dates in Flatpickr with Thai Buddhist display
                    if (fpTravelDocDate && d.doc_date) {
                        fpTravelDocDate.setDate(d.doc_date, true);
                    }
                    if (fpTravelStartDate && d.travel_start_date) {
                        fpTravelStartDate.setDate(d.travel_start_date, true);
                    }
                    if (fpTravelEndDate && d.travel_end_date) {
                        fpTravelEndDate.setDate(d.travel_end_date, true);
                    }

                    $('#travel_dika_no').val(d.dika_no || '');
                    $('#travel_doc_no').val(d.doc_no || '');
                    $('#travel_description').val(d.description || '');
                    $('#travel_destination').val(d.destination || '');
                    $('#travel_days').val(d.travel_days || 1);
                    $('#travel_traveler_count').val(d.traveler_count || 1);
                    $('#travel_traveler_names').val(d.traveler_names || '');

                    $('#travel_allowance_amount').val(d.allowance_amount > 0 ? d.allowance_amount : '');
                    $('#travel_accommodation_amount').val(d.accommodation_amount > 0 ? d.accommodation_amount : '');
                    $('#travel_transport_amount').val(d.transport_amount > 0 ? d.transport_amount : '');
                    $('#travel_other_expenses_amount').val(d.other_expenses_amount > 0 ? d.other_expenses_amount : '');
                    $('#travel_amount_withdraw').val(d.amount_withdraw > 0 ? d.amount_withdraw : '');
                    $('#travel_notes').val(d.notes || '');

                    // Initialize Select2 before assigning values
                    initSelect2();

                    // Set Requester Name in Select2 (with tag support)
                    if (d.requester_name) {
                        var foundReq = false;
                        $('#select_travel_requester option').each(function () {
                            if ($(this).val() === d.requester_name) {
                                $('#select_travel_requester').val(d.requester_name).trigger('change');
                                foundReq = true;
                                return false;
                            }
                        });
                        if (!foundReq) {
                            var newReqOpt = new Option(d.requester_name, d.requester_name, true, true);
                            $('#select_travel_requester').append(newReqOpt).trigger('change');
                        }
                    } else {
                        $('#select_travel_requester').val('').trigger('change');
                    }

                    $('#modalTravelTitle').html('แก้ไขรายการค่าใช้จ่ายเดินทางไปราชการ');
                    $('#modalAddTravel').modal('show');
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
    $('#modalAddTravel').on('hidden.bs.modal', function () {
        $('#formSaveTravel')[0].reset();
        $('#travel_id').val('');
        $('#select_travel_requester').val('').trigger('change');
        $('#travel_days').val(1);
        $('#travel_traveler_count').val(1);
        $('#modalTravelTitle').html('บันทึกรายการเบิกจ่ายค่าใช้จ่ายเดินทางไปราชการ');
        if (fpTravelDocDate) {
            fpTravelDocDate.setDate('today', true);
        }
        if (fpTravelStartDate) {
            fpTravelStartDate.clear();
        }
        if (fpTravelEndDate) {
            fpTravelEndDate.clear();
        }
    });

    // ----------------------------------------------------
    // 10. Delete Item
    // ----------------------------------------------------
    $(document).on('click', '.btn-delete-travel', function () {
        var id = $(this).data('id');
        var desc = $(this).data('desc') || 'รายการนี้';

        Swal.fire({
            title: 'ยืนยันการลบรายการ?',
            html: 'คุณต้องการลบ <b>"' + desc + '"</b> หรือไม่?<br><small class="text-danger">การลบจะคำนวณยอดคงเหลือใหม่โดยอัตโนมัติ</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ลบรายการ',
            cancelButtonText: 'ยกเลิก'
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'กำลังลบ...',
                    allowOutsideClick: false,
                    didOpen: function () {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: window.appBaseUrl + 'Admin/BudgetOrdinance/Travel/Delete/' + id,
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
                            Swal.fire('ผิดพลาด', res.message || 'ไม่สามารถลบรายการได้', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                    }
                });
            }
        });
    });
});
