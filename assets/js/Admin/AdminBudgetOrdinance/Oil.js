/**
 * Admin Budget Ordinance - Oil Reimbursement JS
 * Features:
 *  - Select2 with dropdownParent in Bootstrap Modals
 *  - Thai Buddhist Calendar (Flatpickr พ.ศ.) with year conversion and Thai month formatting
 *  - Real-time calculation (Liters * Price = Total Amount)
 *  - Inputmask Currency
 *  - AJAX Save / Edit / Delete with SweetAlert2
 */

var thaiMonthsFull = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
];



var fpOilDocDate = null;
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

// Update Oil Modal Budget Year Display & Hidden Value
function updateOilModalBudgetYear(dateObj) {
    if (!dateObj) return;
    var fiscalYear = calculateBudgetYearFromDate(dateObj);
    if (fiscalYear) {
        $('#oil_budget_year').val(fiscalYear);
        $('#oilModalYearDisplay').html('ปีงบประมาณ พ.ศ. ' + fiscalYear + ' <span class="badge bg-white text-dark ms-1 fs-9 shadow-xs">อัตโนมัติ</span>');
    }
}

// Update Budget Adjust Modal Year Display & Hidden Value
function updateBudgetAdjustOilBudgetYear(dateObj) {
    if (!dateObj) return;
    var fiscalYear = calculateBudgetYearFromDate(dateObj);
    if (fiscalYear) {
        $('#budget_adjust_oil_year').val(fiscalYear);
        $('#budgetAdjustOilYearDisplay').html('ปีงบประมาณ พ.ศ. ' + fiscalYear + ' <span class="badge bg-white text-dark ms-1 fs-9 shadow-xs">อัตโนมัติ</span>');
        $('#budget_adjust_description').val('จัดสรรงบประมาณค่าน้ำมันเชื้อเพลิง ประจำปีงบประมาณ พ.ศ. ' + fiscalYear);
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

$(document).ready(function () {
    // ----------------------------------------------------
    // 1. Initialize DataTable (Only if data rows exist)
    // ----------------------------------------------------
    if ($('#tableOilLedger').length) {
        if ($('#tableOilLedger tbody td[colspan]').length === 0 && $('#tableOilLedger tbody tr').length > 0) {
            $('#tableOilLedger').DataTable({
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
                var fp = $el.flatpickr({
                    locale: 'th',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'Y-m-d',
                    altInputClass: 'form-control border-start-0 bg-white',
                    defaultDate: 'today',
                    onChange: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0 && instance.altInput) {
                            instance.altInput.value = formatThaiBuddhistDateDisplay(selectedDates[0]);
                            if (elementId === 'oil_doc_date') {
                                updateOilModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_doc_date') {
                                updateBudgetAdjustOilBudgetYear(selectedDates[0]);
                            }
                        }
                    },
                    onReady: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0 && instance.altInput) {
                            instance.altInput.value = formatThaiBuddhistDateDisplay(selectedDates[0]);
                            if (elementId === 'oil_doc_date') {
                                updateOilModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_doc_date') {
                                updateBudgetAdjustOilBudgetYear(selectedDates[0]);
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
                            if (elementId === 'oil_doc_date') {
                                updateOilModalBudgetYear(selectedDates[0]);
                            } else if (elementId === 'budget_adjust_doc_date') {
                                updateBudgetAdjustOilBudgetYear(selectedDates[0]);
                            }
                        }
                    }
                });

                if (elementId === 'oil_doc_date') {
                    fpOilDocDate = fp;
                } else if (elementId === 'budget_adjust_doc_date') {
                    fpBudgetDocDate = fp;
                }
            });
        }
    }
    initBuddhistFlatpickr();

    // ----------------------------------------------------
    // 3. Initialize Select2 (inside Modal with search)
    // ----------------------------------------------------
    function initSelect2() {
        if ($.fn.select2) {
            // Select2 for School Car
            $('#select_school_car').select2({
                dropdownParent: $('#modalAddOil'),
                placeholder: '-- ค้นหา / เลือกรถยนต์โรงเรียน --',
                allowClear: true,
                width: '100%'
            });

            // Select2 with Tags (allows selecting from DB or typing custom)
            $('#select_driver_name').select2({
                dropdownParent: $('#modalAddOil'),
                placeholder: '-- ค้นหา / เลือกคนขับ หรือพิมพ์ชื่อใหม่ --',
                allowClear: true,
                tags: true,
                width: '100%'
            });

            $('#select_requester_name').select2({
                dropdownParent: $('#modalAddOil'),
                placeholder: '-- ค้นหา / เลือกผู้ขอ หรือพิมพ์ชื่อใหม่ --',
                allowClear: true,
                tags: true,
                width: '100%'
            });

            // Select2 Basic for Fuel Type
            $('#oil_fuel_type').select2({
                dropdownParent: $('#modalAddOil'),
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }
    }

    // Initialize Select2 when modal opens to ensure correct dimensions
    $('#modalAddOil').on('shown.bs.modal', function () {
        initSelect2();
    });

    // Handle Click on Add Oil Button (Check if budget is allocated)
    $(document).on('click', '#btnOpenAddOil', function (e) {
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
        $('#modalAddOil').modal('show');
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
    // 5. School Car Selection Change Event
    // ----------------------------------------------------
    $('#select_school_car').on('change', function () {
        var val = $(this).val();
        if (val === 'custom') {
            $('#oil_car_license').val('').focus();
            $('#oil_car_type').val('');
        } else if (val && val !== '') {
            var $opt = $(this).find('option:selected');
            var license = $opt.data('license') || '';
            var type = $opt.data('type') || '';
            $('#oil_car_license').val(license);
            $('#oil_car_type').val(type);
        }
    });

    // ----------------------------------------------------
    // 6. Live Calculation: Liters * Price = Amount
    // ----------------------------------------------------
    $('#oil_fuel_liters, #oil_fuel_price').on('input keyup change', function () {
        var liters = parseFloat($('#oil_fuel_liters').val()) || 0;
        var price = parseFloat($('#oil_fuel_price').val()) || 0;
        if (liters > 0 && price > 0) {
            var total = (liters * price).toFixed(2);
            $('#oil_amount_withdraw').val(total);
        }
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
    // 7. Submit Form: บันทึกการเบิกค่าน้ำมัน (Withdraw)
    // ----------------------------------------------------
    $('#formSaveOil').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnSubmitOil');

        setBtnLoading($btn, true, 'บันทึกข้อมูล');

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Oil/Save',
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
    // 8. Submit Form: ตั้งงบประมาณ / ปรับยอดงบประมาณ (Adjust)
    // ----------------------------------------------------
    $('#formSaveBudgetAdjust').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnSubmitBudget');

        setBtnLoading($btn, true, 'บันทึกงบประมาณ');

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Oil/Save',
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
    // 9. Edit Item (Fetch via AJAX and fill modal)
    // ----------------------------------------------------
    $(document).on('click', '.btn-edit-oil', function () {
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
            url: window.appBaseUrl + 'Admin/BudgetOrdinance/Oil/getOilData/' + id,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                Swal.close();
                if (res.status === 'success' && res.data) {
                    var d = res.data;
                    $('#oil_id').val(d.id);

                    // Set Date in Flatpickr with Thai Buddhist display
                    if (fpOilDocDate && d.doc_date) {
                        fpOilDocDate.setDate(d.doc_date, true);
                    } else {
                        $('#oil_doc_date').val(d.doc_date || '');
                    }

                    $('#oil_dika_no').val(d.dika_no || '');
                    $('#oil_doc_no').val(d.doc_no || '');
                    $('#oil_description').val(d.description || '');
                    $('#oil_destination').val(d.destination || '');
                    $('#oil_car_license').val(d.car_license || '');
                    $('#oil_car_type').val(d.car_type || '');
                    $('#oil_gas_station').val(d.gas_station || '');
                    $('#oil_receipt_no').val(d.receipt_no || '');
                    $('#oil_fuel_liters').val(d.fuel_liters > 0 ? d.fuel_liters : '');
                    $('#oil_fuel_price').val(d.fuel_price_per_liter > 0 ? d.fuel_price_per_liter : '');
                    $('#oil_amount_withdraw').val(d.amount_withdraw > 0 ? d.amount_withdraw : '');
                    $('#oil_notes').val(d.notes || '');

                    // Initialize Select2 before assigning values
                    initSelect2();

                    // Match vehicle dropdown from General DB
                    var matchedCar = false;
                    $('#select_school_car option').each(function () {
                        var optLicense = $(this).data('license');
                        if (optLicense && (optLicense === d.car_license || optLicense.replace(/\s+/g, '') === (d.car_license || '').replace(/\s+/g, ''))) {
                            $('#select_school_car').val($(this).val()).trigger('change.select2');
                            matchedCar = true;
                            return false;
                        }
                    });
                    if (!matchedCar) {
                        $('#select_school_car').val(d.car_license ? 'custom' : '').trigger('change.select2');
                    }

                    // Set Driver Name in Select2 (with tag support)
                    if (d.driver_name) {
                        if ($('#select_driver_name find("option[value=\'' + d.driver_name + '\']"').length) {
                            $('#select_driver_name').val(d.driver_name).trigger('change');
                        } else {
                            var newDriverOpt = new Option(d.driver_name, d.driver_name, true, true);
                            $('#select_driver_name').append(newDriverOpt).trigger('change');
                        }
                    } else {
                        $('#select_driver_name').val('').trigger('change');
                    }

                    // Set Requester Name in Select2 (with tag support)
                    if (d.requester_name) {
                        var foundReq = false;
                        $('#select_requester_name option').each(function () {
                            if ($(this).val() === d.requester_name) {
                                $('#select_requester_name').val(d.requester_name).trigger('change');
                                foundReq = true;
                                return false;
                            }
                        });
                        if (!foundReq) {
                            var newReqOpt = new Option(d.requester_name, d.requester_name, true, true);
                            $('#select_requester_name').append(newReqOpt).trigger('change');
                        }
                    } else {
                        $('#select_requester_name').val('').trigger('change');
                    }

                    // Set Fuel Type in Select2
                    if (d.fuel_type) {
                        $('#oil_fuel_type').val(d.fuel_type).trigger('change');
                    }

                    $('#modalOilTitle').html('แก้ไขรายการเบิกค่าน้ำมันเชื้อเพลิง');
                    $('#modalAddOil').modal('show');
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
    // 10. Reset Modal on Close
    // ----------------------------------------------------
    $('#modalAddOil').on('hidden.bs.modal', function () {
        $('#formSaveOil')[0].reset();
        $('#oil_id').val('');
        $('#select_school_car').val('').trigger('change.select2');
        $('#select_driver_name').val('').trigger('change');
        $('#select_requester_name').val('').trigger('change');
        $('#oil_fuel_type').val('ดีเซล').trigger('change');
        $('#modalOilTitle').html('บันทึกรายการเบิกค่าน้ำมันเชื้อเพลิง');
        if (fpOilDocDate) {
            fpOilDocDate.setDate('today', true);
        }
    });

    // ----------------------------------------------------
    // 11. Delete Item
    // ----------------------------------------------------
    $(document).on('click', '.btn-delete-oil', function () {
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
                    url: window.appBaseUrl + 'Admin/BudgetOrdinance/Oil/Delete/' + id,
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
