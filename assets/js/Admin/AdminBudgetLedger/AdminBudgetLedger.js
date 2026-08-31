/**
 * Admin Budget Ledger & Structure Management JS
 * Handles all AJAX submissions, cascading dropdowns, SweetAlert2 alerts,
 * Thai Buddhist Calendar (Flatpickr), Money Formatting Inputmask,
 * Borrow Contract Linking, Auto-Clearing Calculation, and Real-time Balance Preview.
 */

var thaiMonths = [
    'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
    'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
];

var thaiMonthsShort = [
    'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
];

var ledgerFlatpickr = null;
var cachedBorrows = [];
var isAutoCalculatingClearing = false;

// Helper to parse money string into clean float
function parseMoneyValue(val) {
    if (val === null || val === undefined || val === '') return 0.0;
    if (typeof val === 'number') return val;
    var cleaned = val.toString().replace(/,/g, '').trim();
    var num = parseFloat(cleaned);
    return isNaN(num) ? 0.0 : num;
}

// Helper to format float to currency display string
function formatMoneyDisplay(val) {
    if (val === null || val === undefined || val === '') return '0.00';
    var num = parseFloat(val);
    if (isNaN(num)) return '0.00';
    return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Helper to set button loading state
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

// Update Buddhist Year display inside Flatpickr header
function updateBuddhistYearInCalendar(instance) {
    setTimeout(function() {
        if (instance && instance.currentYearElement) {
            var y = parseInt(instance.currentYearElement.value);
            if (y < 2500) {
                instance.currentYearElement.value = y + 543;
            }
        }
    }, 15);
}

// Real-time calculation preview inside ledger modal
function updateLedgerFormCalculation() {
    var $form = $('#formLedger');
    if (!$form.length) return;

    var prevBalance = parseFloat($form.data('base-balance'));
    if (isNaN(prevBalance)) {
        prevBalance = parseFloat($('#modalLedgerItem').data('last-balance')) || 0.0;
    }

    var adjust = parseMoneyValue($('#amountBudgetAdjust').val());
    var borrow = parseMoneyValue($('#amountBorrow').val());
    var returnCash = parseMoneyValue($('#amountReturnCash').val());
    var directWithdraw = parseMoneyValue($('#amountDirectWithdraw').val());

    // Formula: Net Change = +Adjust -Borrow +ReturnCash -DirectWithdraw
    var netChange = adjust - borrow + returnCash - directWithdraw;
    var newBalance = prevBalance + netChange;

    $('#previewPrevBalance').text(formatMoneyDisplay(prevBalance) + ' ฿');

    var changeSign = netChange > 0 ? '+' : '';
    $('#previewChangeAmount').text(changeSign + formatMoneyDisplay(netChange) + ' ฿');
    
    if (netChange > 0) {
        $('#previewChangeAmount').removeClass('text-danger text-secondary').addClass('text-success');
    } else if (netChange < 0) {
        $('#previewChangeAmount').removeClass('text-success text-secondary').addClass('text-danger');
    } else {
        $('#previewChangeAmount').removeClass('text-success text-danger').addClass('text-secondary');
    }

    $('#previewNewBalance').text(formatMoneyDisplay(newBalance) + ' ฿');
    if (newBalance < 0) {
        $('#previewNewBalance').removeClass('text-success').addClass('text-danger');
        $('#calcWarningAlert').removeClass('d-none');
    } else {
        $('#previewNewBalance').removeClass('text-danger').addClass('text-success');
        $('#calcWarningAlert').addClass('d-none');
    }
}

// Load Uncleared Borrows from API for dropdown
window.loadUnclearedBorrowsForProject = function(projectId, selectedBorrowId, excludeLedgerId, onComplete) {
    var $select = $('#refBorrowSelect');
    $select.html('<option value="">กำลังโหลดรายการสัญญายืมเงิน...</option>');

    var currentRefId = selectedBorrowId || '';
    var excludeId = excludeLedgerId || '';

    $.ajax({
        url: window.appBaseUrl + 'Admin/BudgetLedger/getUnclearedBorrows/' + projectId + '?current_ref_id=' + currentRefId + '&exclude_id=' + excludeId,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            cachedBorrows = res.borrows || [];
            $select.html('<option value="">-- ไม่ระบุ / ส่งใช้นอกสัญญา --</option>');
            
            if (cachedBorrows.length > 0) {
                $.each(cachedBorrows, function(i, b) {
                    var isSelected = (selectedBorrowId && selectedBorrowId == b.id) ? 'selected' : '';
                    var label = 'สัญญา ' + b.doc_no + ' | ยืม ' + formatMoneyDisplay(b.amount_borrow) + ' ฿ (ค้างเคลียร์ ' + formatMoneyDisplay(b.remaining_borrow) + ' ฿)';
                    $select.append('<option value="' + b.id + '" ' + isSelected + ' data-index="' + i + '">' + label + '</option>');
                });
            } else {
                $select.append('<option value="" disabled>-- ไม่มีสัญญายืมเงินที่มียอดค้างชำระ --</option>');
            }

            if (selectedBorrowId) {
                $select.val(selectedBorrowId).trigger('change');
            }

            if (typeof onComplete === 'function') onComplete();
        },
        error: function() {
            $select.html('<option value="">-- เกิดข้อผิดพลาดในการโหลดรายการยืมเงิน --</option>');
        }
    });
};

// Global functions for opening modals and actions
window.openAddTacticModal = function(strategyId, strategyNo, strategyName) {
    $('#formTactic')[0].reset();
    $('#tacticId').val('');
    $('#tacticStrategyId').val(strategyId);
    $('#modalTacticTitle').html('<i class="bx bx-git-branch text-success me-2"></i>1.1 เพิ่มกลยุทธ์ (ยุทธศาสตร์ที่ ' + strategyNo + ')');
    $('#modalAddTactic').modal('show');
};

window.openAddProjectModal = function(strategyId, tacticId, tacticNo) {
    $('#formProject')[0].reset();
    $('#projectId').val('');
    $('#projectStrategySelect').val(strategyId);
    $('#allocatedBudget').val('0.00');
    
    window.loadTacticsForProject(strategyId, tacticId);
    $('#modalProjectTitle').html('<i class="bx bx-folder-plus text-warning me-2"></i>1.1.1 เพิ่มโครงการ (กลยุทธ์ ' + tacticNo + ')');
    $('#modalAddProject').modal('show');
};

window.loadTacticsForProject = function(strategyId, selectedTacticId) {
    var $select = $('#projectTacticSelect');
    $select.html('<option value="">กำลังโหลดกลยุทธ์...</option>');
    
    if (!strategyId) {
        $select.html('<option value="">-- กรุณาเลือกยุทธศาสตร์ก่อน --</option>');
        return;
    }

    $.ajax({
        url: window.appBaseUrl + 'Admin/BudgetLedger/getTacticsByStrategy/' + strategyId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $select.html('<option value="">-- เลือกกลยุทธ์ --</option>');
            if (data && data.length > 0) {
                $.each(data, function(i, t) {
                    var selected = (selectedTacticId && selectedTacticId == t.id) ? 'selected' : '';
                    $select.append('<option value="' + t.id + '" ' + selected + '>กลยุทธ์ ' + t.tactic_no + ': ' + t.tactic_name + '</option>');
                });
            } else {
                $select.html('<option value="">-- ยังไม่มีกลยุทธ์ในยุทธศาสตร์นี้ --</option>');
            }
        },
        error: function() {
            $select.html('<option value="">-- เกิดข้อผิดพลาดในการโหลดกลยุทธ์ --</option>');
        }
    });
};

window.editStrategy = function(data) {
    $('#modalStrategyTitle').html('<i class="bx bx-edit text-primary me-2"></i>แก้ไขยุทธศาสตร์');
    $('#strategyId').val(data.id);
    $('#stratYear').val(data.budget_year);
    $('#stratNo').val(data.strategy_no);
    $('#stratName').val(data.strategy_name);
    $('#modalAddStrategy').modal('show');
};

window.deleteStrategy = function(id) {
    Swal.fire({
        title: 'ยืนยันการลบยุทธศาสตร์?',
        text: 'ข้อมูลกลยุทธ์และโครงการภายใต้ยุทธศาสตร์นี้อาจได้รับผลกระทบ',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({ title: 'กำลังลบข้อมูล...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            $.ajax({
                url: window.appBaseUrl + 'Admin/BudgetLedger/deleteStrategy/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1200, showConfirmButton: false })
                            .then(function() { location.reload(); });
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถลบได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
};

window.editTactic = function(data) {
    $('#modalTacticTitle').html('<i class="bx bx-edit text-success me-2"></i>แก้ไขกลยุทธ์');
    $('#tacticId').val(data.id);
    $('#tacticStrategyId').val(data.strategy_id);
    $('#tacticNo').val(data.tactic_no);
    $('#tacticName').val(data.tactic_name);
    $('#tacticObjective').val(data.objective || '');
    $('#tacticActions').val(data.actions || '');
    $('#tacticResponsibleAgency').val(data.responsible_agency || '');
    $('#modalAddTactic').modal('show');
};

window.deleteTactic = function(id) {
    Swal.fire({
        title: 'ยืนยันการลบกลยุทธ์?',
        text: 'โครงการภายใต้กลยุทธ์นี้อาจไม่มียุทธศาสตร์อ้างอิง',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({ title: 'กำลังลบข้อมูล...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            $.ajax({
                url: window.appBaseUrl + 'Admin/BudgetLedger/deleteTactic/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1200, showConfirmButton: false })
                            .then(function() { location.reload(); });
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถลบได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
};

window.deleteProject = function(id) {
    Swal.fire({
        title: 'ยืนยันการลบโครงการ?',
        text: 'รายการในทะเบียนคุมเงินของโครงการนี้จะถูกลบทั้งหมด',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({ title: 'กำลังลบข้อมูล...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            $.ajax({
                url: window.appBaseUrl + 'Admin/BudgetLedger/deleteProject/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1200, showConfirmButton: false })
                            .then(function() { location.reload(); });
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถลบได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
};

window.deleteLedgerItem = function(id) {
    var projectId = $('#modalLedgerItem').data('project-id');

    Swal.fire({
        title: 'ยืนยันการลบรายการ?',
        text: 'ระบบจะคำนวณยอดคงเหลือใหม่ทั้งหมดหลังจากลบรายการนี้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก'
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({ title: 'กำลังลบรายการ...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            $.ajax({
                url: window.appBaseUrl + 'Admin/BudgetLedger/deleteLedgerItem/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'ลบรายการสำเร็จ', 
                            text: res.message, 
                            timer: 1500, 
                            toast: true, 
                            position: 'top-end', 
                            showConfirmButton: false 
                        });
                        window.refreshLedgerTable(projectId);
                    } else {
                        Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถลบได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
};

// Real-time AJAX refresh for Ledger Table & KPI Cards without page reload
window.refreshLedgerTable = function(projectId, callback) {
    if (!projectId) {
        projectId = $('#modalLedgerItem').data('project-id');
    }
    if (!projectId) return;

    $.ajax({
        url: window.appBaseUrl + 'Admin/BudgetLedger/getLedgerData/' + projectId,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status !== 'success') return;

            // 1. Update 4 KPI Cards
            if (res.totals) {
                $('#statTotalBorrow').text(formatMoneyDisplay(res.totals.borrow));
                $('#statTotalSpent').text(formatMoneyDisplay(res.totals.return_receipt + res.totals.direct_withdraw));
                $('#statNetBalance').text(formatMoneyDisplay(res.totals.last_balance));
                
                // Update modal data-last-balance and base balance
                $('#modalLedgerItem').data('last-balance', res.totals.last_balance);
                $('#formLedger').data('base-balance', res.totals.last_balance);
            }

            // 2. Update Borrows Settlement Section (Active uncleared borrows only)
            var borrows = res.borrows_list || [];
            borrows = borrows.filter(function(b) { return b.remaining_borrow > 0; });

            if (borrows.length > 0) {
                $('#borrowsSectionCard').removeClass('d-none');
                $('#borrowsCountBadge').text('มีสัญญายืมรอเคลียร์ ' + borrows.length + ' รายการ');

                var borrowHtml = '';
                $.each(borrows, function(i, b) {
                    var statusBadge = '<div class="d-flex flex-column align-items-center">' +
                        '<span class="badge bg-label-danger rounded-pill px-2 py-1 fs-8 mb-1"><i class="bx bx-error-circle me-1"></i>ค้าง ' + formatMoneyDisplay(b.remaining_borrow) + ' ฿</span>' +
                        '<div class="progress w-100" style="height: 4px;"><div class="progress-bar bg-warning" role="progressbar" style="width: ' + b.percent + '%"></div></div>' +
                        '</div>';

                    var manageBtn = '<span class="text-muted fs-8">-</span>';
                    if (res.can_edit_ledger) {
                        manageBtn = '<button type="button" class="btn btn-xs btn-primary rounded-pill px-2 py-1" onclick="quickClearBorrow(' + b.id + ', \'' + (b.doc_no || '') + '\', ' + b.remaining_borrow + ')"><i class="bx bx-bolt-circle me-1"></i>ส่งใช้</button>';
                    }

                    borrowHtml += '<tr>' +
                        '<td><span class="badge bg-label-warning font-monospace fs-8">' + (b.doc_no || '-') + '</span></td>' +
                        '<td><span class="text-dark fw-medium">' + (b.description || '') + '</span></td>' +
                        '<td class="text-end fw-bold text-dark">' + formatMoneyDisplay(b.amount_borrow) + '</td>' +
                        '<td class="text-end text-success fw-semibold">' + (b.cleared_receipt > 0 ? formatMoneyDisplay(b.cleared_receipt) : '0.00') + '</td>' +
                        '<td class="text-end text-info fw-semibold">' + (b.cleared_cash > 0 ? formatMoneyDisplay(b.cleared_cash) : '0.00') + '</td>' +
                        '<td class="text-end fw-bold text-danger">' + formatMoneyDisplay(b.remaining_borrow) + '</td>' +
                        '<td class="text-center">' + statusBadge + '</td>' +
                        '<td class="text-center">' + manageBtn + '</td>' +
                        '</tr>';
                });
                $('#borrowsTableBody').html(borrowHtml);
            } else {
                $('#borrowsSectionCard').addClass('d-none');
                $('#borrowsTableBody').html('');
            }

            // 2.1 Update Sub-Items Breakdown Table if exists
            if (res.sub_items && res.sub_items.length > 0) {
                var subHtml = '';
                $.each(res.sub_items, function(i, sub) {
                    var progressBg = sub.percent >= 90 ? 'bg-danger' : (sub.percent >= 50 ? 'bg-warning' : 'bg-success');
                    var remClass = sub.remaining < 0 ? 'text-danger' : 'text-success';
                    subHtml += '<tr>' +
                        '<td class="text-center"><span class="badge bg-label-dark fw-bold">' + (sub.item_no || '-') + '</span></td>' +
                        '<td><span class="fw-bold text-dark">' + sub.item_name + '</span></td>' +
                        '<td class="text-end fw-semibold text-primary">' + formatMoneyDisplay(sub.allocated_budget) + ' ฿</td>' +
                        '<td class="text-end fw-semibold text-danger">' + formatMoneyDisplay(sub.total_spent) + ' ฿</td>' +
                        '<td class="text-end fw-bold ' + remClass + '">' + formatMoneyDisplay(sub.remaining) + ' ฿</td>' +
                        '<td class="text-center">' +
                            '<div class="d-flex align-items-center gap-2">' +
                                '<div class="progress flex-grow-1" style="height: 6px;">' +
                                    '<div class="progress-bar ' + progressBg + '" role="progressbar" style="width: ' + sub.percent + '%"></div>' +
                                '</div>' +
                                '<span class="fs-8 fw-semibold text-muted">' + sub.percent + '%</span>' +
                            '</div>' +
                        '</td>' +
                        '</tr>';
                });
                $('#subItemsSummaryBody').html(subHtml);
            }

            // 3. Update Main Ledger Table
            var ledgerRows = res.ledger || [];
            $('#ledgerRecordCountBadge').text(ledgerRows.length + ' รายการบันทึก');

            if (ledgerRows.length === 0) {
                $('#ledgerTableBody').html('<tr id="emptyLedgerRow"><td colspan="10" class="text-center py-5 text-muted"><i class="bx bx-file display-4 text-muted opacity-50 mb-2 d-block"></i>ยังไม่มีรายการบันทึกในทะเบียนคุมนี้</td></tr>');
            } else {
                var rowsHtml = '';
                $.each(ledgerRows, function(i, row) {
                    var subBadge = row.sub_item_no ? '<span class="badge bg-label-warning me-1 fs-8" title="' + (row.sub_item_name || '') + '"><i class="bx bx-subdirectory-right me-1"></i>' + row.sub_item_no + '</span>' : '';
                    var refBadge = ((row.amount_return_cash > 0 || row.amount_return_receipt > 0) && row.ref_doc_no) ? ' <span class="badge bg-label-info font-monospace fs-8 ms-1" title="อ้างอิงสัญญายืมเงิน"><i class="bx bx-link me-1"></i>ล้างสัญญา: ' + row.ref_doc_no + '</span>' : '';
                    var notesHtml = row.notes ? '<small class="text-muted d-block mt-1"><i class="bx bx-info-circle me-1"></i>' + row.notes + '</small>' : '';
                    
                    var balClass = row.balance < 0 ? 'text-danger' : 'text-dark';
                    var manageCell = '<span class="text-muted fs-8">-</span>';

                    if (res.can_edit_ledger) {
                        var jsonAttr = $('<div>').text(row.raw_json).html();
                        manageCell = '<div class="d-inline-flex gap-1">' +
                            '<button type="button" class="btn btn-xs btn-icon btn-outline-secondary rounded-circle btn-edit-ledger" data-ledger=\'' + jsonAttr + '\' title="แก้ไขรายการ"><i class="bx bx-edit-alt"></i></button>' +
                            '<button type="button" class="btn btn-xs btn-icon btn-outline-danger rounded-circle" onclick="deleteLedgerItem(' + row.id + ')" title="ลบรายการ"><i class="bx bx-trash"></i></button>' +
                            '</div>';
                    }

                    rowsHtml += '<tr>' +
                        '<td class="text-center"><span class="fw-semibold text-dark">' + (row.display_date || '-') + '</span></td>' +
                        '<td class="text-center fw-bold text-primary">' + (row.doc_no || '-') + '</td>' +
                        '<td style="white-space: normal; min-width: 260px; max-width: 380px;">' + subBadge + '<span class="text-dark fw-medium">' + (row.description || '') + '</span>' + refBadge + notesHtml + '</td>' +
                        '<td class="text-end fw-bold text-primary">' + (row.amount_budget_adjust > 0 ? formatMoneyDisplay(row.amount_budget_adjust) : '') + '</td>' +
                        '<td class="text-end text-warning fw-semibold">' + (row.amount_borrow > 0 ? formatMoneyDisplay(row.amount_borrow) : '') + '</td>' +
                        '<td class="text-end text-info fw-semibold">' + (row.amount_return_cash > 0 ? formatMoneyDisplay(row.amount_return_cash) : '') + '</td>' +
                        '<td class="text-end text-success fw-semibold">' + (row.amount_return_receipt > 0 ? formatMoneyDisplay(row.amount_return_receipt) : '') + '</td>' +
                        '<td class="text-end text-danger fw-semibold">' + (row.amount_direct_withdraw > 0 ? formatMoneyDisplay(row.amount_direct_withdraw) : '') + '</td>' +
                        '<td class="text-end fw-bold fs-7 ' + balClass + '" style="background-color: #fcfcfd;">' + formatMoneyDisplay(row.balance) + '</td>' +
                        '<td class="text-center">' + manageCell + '</td>' +
                        '</tr>';
                });
                $('#ledgerTableBody').html(rowsHtml);
            }

            // 4. Update Footer Totals
            if (res.totals) {
                var footHtml = '<tr style="background-color: #f8f9fa;">' +
                    '<td colspan="3" class="text-center text-dark">รวมทั้งสิ้น</td>' +
                    '<td class="text-primary">' + formatMoneyDisplay(res.totals.adjust) + '</td>' +
                    '<td class="text-warning">' + formatMoneyDisplay(res.totals.borrow) + '</td>' +
                    '<td class="text-info">' + formatMoneyDisplay(res.totals.return_cash) + '</td>' +
                    '<td class="text-success">' + formatMoneyDisplay(res.totals.return_receipt) + '</td>' +
                    '<td class="text-danger">' + formatMoneyDisplay(res.totals.direct_withdraw) + '</td>' +
                    '<td class="text-dark bg-white fs-7">' + formatMoneyDisplay(res.totals.last_balance) + '</td>' +
                    '<td></td>' +
                    '</tr>';
                $('#ledgerTableFoot').html(footHtml);
            }

            if (typeof callback === 'function') callback();
        }
    });
};

// Switch Entry Type in Modal
window.switchEntryType = function(type) {
    var projectId = $('#modalLedgerItem').data('project-id');

    if (type === 'return') {
        $('#borrowRefContainer').removeClass('d-none');
        window.loadUnclearedBorrowsForProject(projectId, $('#refBorrowSelect').val(), $('#ledgerId').val());
        $('#amountReturnReceipt').focus().select();
    } else {
        // ALWAYS CLEAR BORROW REFERENCE WHEN NOT RETURN!
        $('#borrowRefContainer').addClass('d-none');
        $('#borrowDetailBox').addClass('d-none');
        $('#refBorrowSelect').val('');
        $('#refDocNo').val('');
        $('#borrowRemainingBadge').text('ไม่ได้เลือกสัญญา').removeClass('bg-label-success bg-label-danger bg-label-warning').addClass('bg-label-secondary');

        if (type === 'borrow') {
            $('#amountReturnCash').val('0.00');
            $('#amountReturnReceipt').val('0.00');
            $('#amountBorrow').focus().select();
        } else if (type === 'direct') {
            $('#amountReturnCash').val('0.00');
            $('#amountReturnReceipt').val('0.00');
            $('#amountDirectWithdraw').focus().select();
        } else if (type === 'adjust') {
            $('#amountReturnCash').val('0.00');
            $('#amountReturnReceipt').val('0.00');
            $('#amountBudgetAdjust').focus().select();
        }
    }
    updateLedgerFormCalculation();
};

// Quick button from Borrows Table to open modal and preselect borrow contract
window.quickClearBorrow = function(borrowId, docNo, remainingAmount) {
    $('#formLedger')[0].reset();
    $('#ledgerId').val('');
    $('input[name="entry_type"]').prop('checked', false);
    $('#typeReturn').prop('checked', true);

    var lastBal = parseFloat($('#modalLedgerItem').data('last-balance')) || 0.0;
    $('#formLedger').data('base-balance', lastBal);
    $('#calcModeBadge').text('ส่งใช้เงินยืม');

    $('#modalLedgerTitle').html('<i class="bx bx-check-shield text-success me-2"></i>ส่งใช้เงินยืม (สัญญา: ' + docNo + ')');
    $('#ledgerDescription').val('ส่งใช้เงินยืมตามสัญญา ' + docNo);

    var projectId = $('#modalLedgerItem').data('project-id');
    $('#borrowRefContainer').removeClass('d-none');
    
    window.loadUnclearedBorrowsForProject(projectId, borrowId, null, function() {
        $('#amountReturnReceipt').val(formatMoneyDisplay(remainingAmount));
        $('#amountReturnCash').val('0.00');
        updateLedgerFormCalculation();
    });

    $('#modalLedgerItem').modal('show');
};

$(document).ready(function () {
    "use strict";

    // ----------------------------------------------------
    // 1. Initialize Money Inputmask (.input-money)
    // ----------------------------------------------------
    if ($.fn.inputmask) {
        $('.input-money').inputmask({
            alias: 'decimal',
            groupSeparator: ',',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            radixPoint: '.',
            rightAlign: true,
            allowMinus: false,
            clearMaskOnLostFocus: false
        });
    }

    // Auto highlight text on click/focus for easier editing
    $(document).on('focus', '.input-money', function() {
        var $this = $(this);
        setTimeout(function() {
            $this.select();
        }, 50);
    });

    // ----------------------------------------------------
    // 2. Borrow Document Selection Change Handler
    // ----------------------------------------------------
    $('#refBorrowSelect').on('change', function() {
        var selectedId = $(this).val();
        if (!selectedId) {
            $('#borrowDetailBox').addClass('d-none');
            $('#borrowRemainingBadge').text('ไม่ได้เลือกสัญญา').removeClass('bg-label-success bg-label-danger').addClass('bg-label-secondary');
            $('#refDocNo').val('');
            return;
        }

        var found = cachedBorrows.find(function(b) { return b.id == selectedId; });
        if (found) {
            $('#borrowDetailBox').removeClass('d-none');
            $('#boxBorrowAmount').text(formatMoneyDisplay(found.amount_borrow) + ' ฿');
            $('#boxClearedAmount').text(formatMoneyDisplay(found.total_cleared) + ' ฿');
            $('#boxRemainingAmount').text(formatMoneyDisplay(found.remaining_borrow) + ' ฿');

            $('#refDocNo').val(found.doc_no || '');
            $('#borrowRemainingBadge').text('ค้างเคลียร์ ' + formatMoneyDisplay(found.remaining_borrow) + ' ฿')
                .removeClass('bg-label-secondary bg-label-success').addClass('bg-label-warning');

            // Pre-fill description if currently blank
            if (!$('#ledgerDescription').val()) {
                $('#ledgerDescription').val('ส่งใช้เงินยืมตามสัญญา ' + found.doc_no);
            }
        }
    });

    // ----------------------------------------------------
    // 3. Auto-Calculate Between Return Receipt & Return Cash
    // ----------------------------------------------------
    $('#amountReturnReceipt').on('input keyup change', function() {
        if (isAutoCalculatingClearing) return;
        var selectedId = $('#refBorrowSelect').val();
        if (!selectedId) return;

        var found = cachedBorrows.find(function(b) { return b.id == selectedId; });
        if (found && found.remaining_borrow > 0) {
            var receiptVal = parseMoneyValue($(this).val());
            var autoCash = Math.max(0, found.remaining_borrow - receiptVal);
            
            isAutoCalculatingClearing = true;
            $('#amountReturnCash').val(formatMoneyDisplay(autoCash));
            isAutoCalculatingClearing = false;
        }
        updateLedgerFormCalculation();
    });

    $('#amountReturnCash').on('input keyup change', function() {
        if (isAutoCalculatingClearing) return;
        var selectedId = $('#refBorrowSelect').val();
        if (!selectedId) return;

        var found = cachedBorrows.find(function(b) { return b.id == selectedId; });
        if (found && found.remaining_borrow > 0) {
            var cashVal = parseMoneyValue($(this).val());
            var autoReceipt = Math.max(0, found.remaining_borrow - cashVal);
            
            isAutoCalculatingClearing = true;
            $('#amountReturnReceipt').val(formatMoneyDisplay(autoReceipt));
            isAutoCalculatingClearing = false;
        }
        updateLedgerFormCalculation();
    });

    // Quick Fill Buttons for Return
    $('#btnFillAllReceipt').on('click', function() {
        var selectedId = $('#refBorrowSelect').val();
        var found = cachedBorrows.find(function(b) { return b.id == selectedId; });
        var remaining = found ? found.remaining_borrow : 0.0;
        
        isAutoCalculatingClearing = true;
        $('#amountReturnReceipt').val(formatMoneyDisplay(remaining));
        $('#amountReturnCash').val('0.00');
        isAutoCalculatingClearing = false;
        updateLedgerFormCalculation();
    });

    $('#btnFillAllCash').on('click', function() {
        var selectedId = $('#refBorrowSelect').val();
        var found = cachedBorrows.find(function(b) { return b.id == selectedId; });
        var remaining = found ? found.remaining_borrow : 0.0;
        
        isAutoCalculatingClearing = true;
        $('#amountReturnCash').val(formatMoneyDisplay(remaining));
        $('#amountReturnReceipt').val('0.00');
        isAutoCalculatingClearing = false;
        updateLedgerFormCalculation();
    });

    // Listen to all money inputs for preview
    $(document).on('input keyup change', '#formLedger .input-money', function() {
        updateLedgerFormCalculation();
    });

    // ----------------------------------------------------
    // 4. Initialize Thai Buddhist Calendar (Flatpickr)
    // ----------------------------------------------------
    function formatThaiBuddhistAlt(selectedDates, instance) {
        if (selectedDates && selectedDates.length > 0 && instance && instance.altInput) {
            var d = selectedDates[0];
            var day = d.getDate();
            var m = thaiMonths[d.getMonth()];
            var y = d.getFullYear();
            if (y < 2400) {
                y += 543;
            }
            instance.altInput.value = day + ' ' + m + ' ' + y;
        }
    }

    if (typeof flatpickr !== 'undefined' && $('#ledgerDocDate').length) {
        flatpickr.localize(flatpickr.l10ns.th);
        ledgerFlatpickr = $('#ledgerDocDate').flatpickr({
            locale: "th",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "Y-m-d",
            altInputClass: "form-control selector-date bg-white",
            onChange: function(selectedDates, dateStr, instance) {
                formatThaiBuddhistAlt(selectedDates, instance);
            },
            onReady: function(selectedDates, dateStr, instance) {
                formatThaiBuddhistAlt(selectedDates, instance);
                updateBuddhistYearInCalendar(instance);
            },
            onMonthChange: function(selectedDates, dateStr, instance) {
                updateBuddhistYearInCalendar(instance);
            },
            onYearChange: function(selectedDates, dateStr, instance) {
                updateBuddhistYearInCalendar(instance);
            },
            onOpen: function(selectedDates, dateStr, instance) {
                formatThaiBuddhistAlt(selectedDates, instance);
                updateBuddhistYearInCalendar(instance);
            },
            onClose: function(selectedDates, dateStr, instance) {
                formatThaiBuddhistAlt(selectedDates, instance);
            }
        });
    }

    // ----------------------------------------------------
    // 5. Form Strategy Submit (ระดับ 1)
    // ----------------------------------------------------
    $('#formStrategy').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSubmitStrategy');
        setBtnLoading($btn, true, 'บันทึกยุทธศาสตร์');

        var formData = new FormData(this);

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetLedger/saveStrategy',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                setBtnLoading($btn, false, 'บันทึกยุทธศาสตร์');
                if (res.status === 'success') {
                    $('#modalAddStrategy').modal('hide');
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(function () { location.reload(); });
                } else {
                    Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function (xhr) {
                setBtnLoading($btn, false, 'บันทึกยุทธศาสตร์');
                var errMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
                if (xhr.responseJSON && xhr.responseJSON.message) errMsg = xhr.responseJSON.message;
                Swal.fire('ข้อผิดพลาด', errMsg, 'error');
            }
        });
    });

    // ----------------------------------------------------
    // 6. Form Tactic Submit (ระดับ 2)
    // ----------------------------------------------------
    $('#formTactic').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSubmitTactic');
        setBtnLoading($btn, true, 'บันทึกกลยุทธ์');

        var formData = new FormData(this);

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetLedger/saveTactic',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                setBtnLoading($btn, false, 'บันทึกกลยุทธ์');
                if (res.status === 'success') {
                    $('#modalAddTactic').modal('hide');
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(function () { location.reload(); });
                } else {
                    Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function (xhr) {
                setBtnLoading($btn, false, 'บันทึกกลยุทธ์');
                var errMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
                if (xhr.responseJSON && xhr.responseJSON.message) errMsg = xhr.responseJSON.message;
                Swal.fire('ข้อผิดพลาด', errMsg, 'error');
            }
        });
    });

    // ----------------------------------------------------
    // 7. Form Project Submit (ระดับ 3)
    // ----------------------------------------------------
    $('#formProject').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSubmitProject');
        setBtnLoading($btn, true, 'บันทึกโครงการ');

        var formData = new FormData(this);

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetLedger/saveProject',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                setBtnLoading($btn, false, 'บันทึกโครงการ');
                if (res.status === 'success') {
                    $('#modalAddProject').modal('hide');
                    Swal.fire({ icon: 'success', title: 'สำเร็จ', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(function () { location.reload(); });
                } else {
                    Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function (xhr) {
                setBtnLoading($btn, false, 'บันทึกโครงการ');
                var errMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
                if (xhr.responseJSON && xhr.responseJSON.message) errMsg = xhr.responseJSON.message;
                Swal.fire('ข้อผิดพลาด', errMsg, 'error');
            }
        });
    });

    // ----------------------------------------------------
    // Sub-items Helper Functions in Modal Project
    // ----------------------------------------------------
    var subItemIndex = 0;

    function renderSubItemRow(itemNo, itemName, allocatedBudget) {
        var idx = subItemIndex++;
        var budgetVal = allocatedBudget !== undefined ? formatMoneyDisplay(allocatedBudget) : '0.00';
        var html = '<tr class="sub-item-row" data-index="' + idx + '">' +
            '<td><input type="text" class="form-control form-control-sm sub-item-no" name="sub_items[' + idx + '][item_no]" value="' + (itemNo || '') + '" placeholder="เช่น ๘.๑"></td>' +
            '<td><input type="text" class="form-control form-control-sm sub-item-name" name="sub_items[' + idx + '][item_name]" value="' + (itemName || '') + '" placeholder="ชื่อรายการ เช่น ค่าอุปกรณ์การเรียน"></td>' +
            '<td><input type="text" class="form-control form-control-sm text-end input-money sub-item-budget" name="sub_items[' + idx + '][allocated_budget]" value="' + budgetVal + '"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-xs btn-icon btn-outline-danger rounded-circle btn-remove-sub-item"><i class="bx bx-trash"></i></button></td>' +
            '</tr>';
        
        var $row = $(html);
        $('#subItemsContainer').append($row);

        // Apply Inputmask
        if ($.fn.inputmask) {
            $row.find('.input-money').inputmask({
                alias: 'numeric',
                groupSeparator: ',',
                autoGroup: true,
                digits: 2,
                digitsOptional: false,
                placeholder: '0.00',
                rightAlign: true,
                allowMinus: false,
                clearMaskOnLostFocus: false
            });
        }

        updateSubItemsTotal();
    }

    function updateSubItemsTotal() {
        var rowCount = $('#subItemsContainer tr.sub-item-row').length;
        if (rowCount > 0) {
            $('#subItemsTotalRow').show();
            var total = 0.0;
            $('#subItemsContainer .sub-item-budget').each(function () {
                total += parseMoneyValue($(this).val());
            });
            $('#subItemsTotalDisplay').text(formatMoneyDisplay(total) + ' บาท');
            
            // Auto update allocated budget of main project
            if (total > 0) {
                $('#allocatedBudget').val(formatMoneyDisplay(total));
            }
        } else {
            $('#subItemsTotalRow').hide();
        }
    }

    $('#btnAddSubItemRow').on('click', function () {
        renderSubItemRow('', '', 0);
    });

    $(document).on('click', '.btn-remove-sub-item', function () {
        $(this).closest('tr.sub-item-row').remove();
        updateSubItemsTotal();
    });

    $(document).on('input keyup change', '.sub-item-budget', function () {
        updateSubItemsTotal();
    });

    // Edit Project Click
    $(document).on('click', '.btn-edit-project', function () {
        var data = $(this).data('project');
        if (typeof data === 'string') {
            try { data = JSON.parse(data); } catch (e) {}
        }
        $('#modalProjectTitle').html('<i class="bx bx-edit text-warning me-2"></i>แก้ไขโครงการ');
        $('#projectId').val(data.id);
        $('#projectYear').val(data.budget_year);
        $('#projectStrategySelect').val(data.strategy_id || '');
        window.loadTacticsForProject(data.strategy_id, data.tactic_id);
        $('#projectCode').val(data.project_code || '');
        $('#projectName').val(data.project_name);
        $('#budgetSource').val(data.budget_source || '');
        $('#allocatedBudget').val(formatMoneyDisplay(data.allocated_budget));
        
        // Populate sub-items
        $('#subItemsContainer').empty();
        if (data.sub_items && data.sub_items.length > 0) {
            $.each(data.sub_items, function (i, sub) {
                renderSubItemRow(sub.item_no, sub.item_name, sub.allocated_budget);
            });
        }
        updateSubItemsTotal();

        $('#modalAddProject').modal('show');
    });

    // ----------------------------------------------------
    // 8. Form Ledger Item Submit
    // ----------------------------------------------------
    $('#formLedger').on('submit', function (e) {
        e.preventDefault();

        // 1. Validate doc_date (วัน/เดือน/ปี)
        var docDateVal = $('#ledgerDocDate').val();
        if (ledgerFlatpickr && ledgerFlatpickr.selectedDates && ledgerFlatpickr.selectedDates.length > 0) {
            var selected = ledgerFlatpickr.selectedDates[0];
            var y = selected.getFullYear();
            var m = String(selected.getMonth() + 1).padStart(2, '0');
            var d = String(selected.getDate()).padStart(2, '0');
            docDateVal = y + '-' + m + '-' + d;
        }

        if (!docDateVal) {
            Swal.fire('ข้อมูลไม่ครบถ้วน', 'กรุณาระบุ <b>วัน/เดือน/ปี</b>', 'warning').then(function() {
                if (ledgerFlatpickr) ledgerFlatpickr.open();
                else $('#ledgerDocDate').focus();
            });
            return;
        }

        // 2. Validate doc_no (ที่เอกสาร)
        var docNoVal = $.trim($('#ledgerDocNo').val());
        if (!docNoVal) {
            Swal.fire('ข้อมูลไม่ครบถ้วน', 'กรุณาระบุ <b>ที่เอกสาร</b> (เช่น 15/2569 หรือ เลขที่ฎีกา/หนังสือ)', 'warning').then(function() {
                $('#ledgerDocNo').focus();
            });
            return;
        }

        // 3. Validate description (รายการ / วัตถุประสงค์)
        var descVal = $.trim($('#ledgerDescription').val());
        if (!descVal) {
            Swal.fire('ข้อมูลไม่ครบถ้วน', 'กรุณาระบุ <b>รายการ / วัตถุประสงค์การใช้จ่าย</b>', 'warning').then(function() {
                $('#ledgerDescription').focus();
            });
            return;
        }

        // 4. Validate that at least one money amount is entered
        var adj = parseMoneyValue($('#amountBudgetAdjust').val());
        var bor = parseMoneyValue($('#amountBorrow').val());
        var retCash = parseMoneyValue($('#amountReturnCash').val());
        var retRec = parseMoneyValue($('#amountReturnReceipt').val());
        var dir = parseMoneyValue($('#amountDirectWithdraw').val());

        if (adj === 0 && bor === 0 && retCash === 0 && retRec === 0 && dir === 0) {
            Swal.fire('กรุณาระบุจำนวนเงิน', 'ต้องระบุจำนวนเงินอย่างน้อย 1 ช่องรายการ (ปรับยอดงบ, ยืมเงิน, ส่งใช้เงินสด, ส่งใช้ใบสำคัญ หรือเบิกตรง)', 'warning');
            return;
        }

        var $btn = $('#btnSubmitLedger');
        setBtnLoading($btn, true, 'บันทึกรายการ');

        var formData = new FormData(this);
        formData.set('doc_date', docDateVal);
        formData.set('doc_no', docNoVal);
        formData.set('description', descVal);

        $.ajax({
            url: window.appBaseUrl + 'Admin/BudgetLedger/saveLedgerItem',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                setBtnLoading($btn, false, 'บันทึกรายการ');
                if (res.status === 'success') {
                    var projectId = $('#modalLedgerItem').data('project-id');
                    $('#modalLedgerItem').modal('hide');
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'บันทึกสำเร็จ', 
                        text: res.message, 
                        timer: 1500, 
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false 
                    });
                    window.refreshLedgerTable(projectId);
                } else {
                    Swal.fire('ข้อผิดพลาด', res.message || 'ไม่สามารถบันทึกได้', 'error');
                }
            },
            error: function (xhr) {
                setBtnLoading($btn, false, 'บันทึกรายการ');
                var errMsg = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
                if (xhr.responseJSON && xhr.responseJSON.message) errMsg = xhr.responseJSON.message;
                Swal.fire('ข้อผิดพลาด', errMsg, 'error');
            }
        });
    });

    // ----------------------------------------------------
    // 9. Edit Ledger Item Click
    // ----------------------------------------------------
    $(document).on('click', '.btn-edit-ledger', function () {
        var data = $(this).data('ledger');
        if (typeof data === 'string') {
            try { data = JSON.parse(data); } catch (e) {}
        }
        $('#modalLedgerTitle').html('<i class="bx bx-edit text-primary me-2"></i>แก้ไขรายการในทะเบียนคุม');
        $('#ledgerId').val(data.id);
        
        if (ledgerFlatpickr) {
            if (data.doc_date && data.doc_date !== '0000-00-00') {
                ledgerFlatpickr.setDate(data.doc_date, true);
            } else {
                ledgerFlatpickr.clear();
            }
        } else {
            $('#ledgerDocDate').val(data.doc_date || '');
        }

        $('#ledgerDocNo').val(data.doc_no || '');
        $('#ledgerDescription').val(data.description || '');
        $('#ledgerSubItemId').val(data.sub_item_id || '');
        $('#amountBudgetAdjust').val(formatMoneyDisplay(data.amount_budget_adjust));
        $('#amountBorrow').val(formatMoneyDisplay(data.amount_borrow));
        $('#amountReturnCash').val(formatMoneyDisplay(data.amount_return_cash));
        $('#amountReturnReceipt').val(formatMoneyDisplay(data.amount_return_receipt));
        $('#amountDirectWithdraw').val(formatMoneyDisplay(data.amount_direct_withdraw));
        $('#ledgerNotes').val(data.notes || '');

        var projectId = $('#modalLedgerItem').data('project-id');

        // Check if this is a return transaction
        if (parseFloat(data.amount_return_cash) > 0 || parseFloat(data.amount_return_receipt) > 0 || data.ref_borrow_id) {
            $('#typeReturn').prop('checked', true);
            $('#borrowRefContainer').removeClass('d-none');
            window.loadUnclearedBorrowsForProject(projectId, data.ref_borrow_id, data.id);
        } else if (parseFloat(data.amount_borrow) > 0) {
            $('#typeBorrow').prop('checked', true);
            $('#borrowRefContainer').addClass('d-none');
        } else if (parseFloat(data.amount_direct_withdraw) > 0) {
            $('#typeDirect').prop('checked', true);
            $('#borrowRefContainer').addClass('d-none');
        } else if (parseFloat(data.amount_budget_adjust) > 0) {
            $('#typeAdjust').prop('checked', true);
            $('#borrowRefContainer').addClass('d-none');
        } else {
            $('#borrowRefContainer').addClass('d-none');
        }

        // Calculate base balance before this item
        var itemBal = parseFloat(data.balance) || 0.0;
        var adj = parseFloat(data.amount_budget_adjust) || 0.0;
        var bor = parseFloat(data.amount_borrow) || 0.0;
        var ret = parseFloat(data.amount_return_cash) || 0.0;
        var dir = parseFloat(data.amount_direct_withdraw) || 0.0;

        var baseBalance = itemBal - adj + bor - ret + dir;
        $('#formLedger').data('base-balance', baseBalance);
        $('#calcModeBadge').text('แก้ไขรายการ');

        updateLedgerFormCalculation();
        $('#modalLedgerItem').modal('show');
    });

    // When modal is shown for a NEW entry, setup base balance
    $('#modalLedgerItem').on('show.bs.modal', function () {
        if (!$('#ledgerId').val()) {
            var lastBal = parseFloat($('#modalLedgerItem').data('last-balance')) || 0.0;
            $('#formLedger').data('base-balance', lastBal);
            $('#calcModeBadge').text('รายการใหม่');
            updateLedgerFormCalculation();
        }
    });

    // ----------------------------------------------------
    // 10. Modal Resets on Hidden
    // ----------------------------------------------------
    $('#modalAddStrategy').on('hidden.bs.modal', function () {
        $('#formStrategy')[0].reset();
        $('#strategyId').val('');
        $('#modalStrategyTitle').html('<i class="bx bx-flag text-primary me-2"></i>1. เพิ่มยุทธศาสตร์ (ระดับที่ 1)');
    });

    $('#modalAddTactic').on('hidden.bs.modal', function () {
        $('#formTactic')[0].reset();
        $('#tacticId').val('');
        $('#modalTacticTitle').html('<i class="bx bx-git-branch text-success me-2"></i>1.1 เพิ่มกลยุทธ์ (ระดับที่ 2)');
    });

    $('#modalAddProject').on('hidden.bs.modal', function () {
        $('#formProject')[0].reset();
        $('#projectId').val('');
        $('#allocatedBudget').val('0.00');
        $('#subItemsContainer').empty();
        $('#subItemsTotalRow').hide();
        $('#modalProjectTitle').html('<i class="bx bx-folder-plus text-warning me-2"></i>1.1.1 เพิ่มโครงการและงบประมาณ (ระดับที่ 3)');
    });

    // Auto-fill / Prefix description on selecting sub-item
    $('#ledgerSubItemId').on('change', function () {
        var subId = $(this).val();
        if (!subId) return;

        var $selected = $(this).find('option:selected');
        var subNo = $selected.data('no');
        var subName = $selected.data('name');

        var currentDesc = $('#ledgerDescription').val();
        var prefix = '[' + (subNo ? subNo + ' ' : '') + subName + '] ';

        // If description is empty or starts with previous brackets, replace/prepend
        if (!currentDesc) {
            $('#ledgerDescription').val(prefix);
        } else if (!currentDesc.includes(subName)) {
            $('#ledgerDescription').val(prefix + currentDesc);
        }
    });

    $('#modalLedgerItem').on('hidden.bs.modal', function () {
        $('#formLedger')[0].reset();
        $('#ledgerId').val('');
        $('#ledgerSubItemId').val('');
        if (ledgerFlatpickr) ledgerFlatpickr.clear();
        $('#amountBudgetAdjust').val('0.00');
        $('#amountBorrow').val('0.00');
        $('#amountReturnCash').val('0.00');
        $('#amountReturnReceipt').val('0.00');
        $('#amountDirectWithdraw').val('0.00');
        $('#borrowRefContainer').addClass('d-none');
        $('#borrowDetailBox').addClass('d-none');
        $('#refBorrowSelect').val('');
        $('#refDocNo').val('');
        $('#borrowRemainingBadge').text('ไม่ได้เลือกสัญญา').removeClass('bg-label-success bg-label-danger bg-label-warning').addClass('bg-label-secondary');
        
        var lastBal = parseFloat($('#modalLedgerItem').data('last-balance')) || 0.0;
        $('#formLedger').data('base-balance', lastBal);
        updateLedgerFormCalculation();
    });
});
