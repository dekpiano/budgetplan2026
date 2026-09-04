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
    $('#allocatedBudget').val('0.00').prop('readonly', false);
    $('#mainActivitiesContainer').empty();
    mainActivityIndex = 0;
    $('#emptyActivityAlert').show();
    $('#subItemsTotalRow').addClass('d-none');
    $('#allocatedBudgetAutoBadge').addClass('d-none');
    $('#subItemsTotalDisplay').text('0.00 บาท');
    
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

window.formatItemLines = function(text) {
    if (!text) return '';
    var t = String(text).replace(/\r\n|\r/g, '\n');
    t = t.replace(/([^\n\r])\s+(?=(\d{1,2}|[๑-๙]{1,2})[\.\)](\s|[^\d\.\s]))/gu, '$1\n');
    t = t.replace(/([^\n\r])\s+(?=[-•*]\s+)/gu, '$1\n');
    return t.trim();
};

window.editTactic = function(data) {
    $('#modalTacticTitle').html('<i class="bx bx-edit text-success me-2"></i>แก้ไขกลยุทธ์');
    $('#tacticId').val(data.id);
    $('#tacticStrategyId').val(data.strategy_id);
    $('#tacticNo').val(data.tactic_no);
    $('#tacticName').val(data.tactic_name);
    $('#tacticObjective').val(window.formatItemLines(data.objective || ''));
    $('#tacticActions').val(window.formatItemLines(data.actions || ''));
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

    // Format actions & objective on blur
    $('#tacticActions, #tacticObjective').on('blur', function() {
        var val = $(this).val();
        if (val && window.formatItemLines) {
            $(this).val(window.formatItemLines(val));
        }
    });

    // ----------------------------------------------------
    // 6. Form Tactic Submit (ระดับ 2)
    // ----------------------------------------------------
    $('#formTactic').on('submit', function (e) {
        e.preventDefault();

        if (window.formatItemLines) {
            $('#tacticActions').val(window.formatItemLines($('#tacticActions').val()));
            $('#tacticObjective').val(window.formatItemLines($('#tacticObjective').val()));
        }

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
    // Hierarchical Activities (Level 4 & 5) in Modal Project
    // ----------------------------------------------------
    var mainActivityIndex = 0;

    function applyInputMask($el) {
        if ($.fn.inputmask) {
            $el.inputmask({
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
    }

    function renderMainActivity(mainData) {
        mainData = mainData || {};
        var mIdx = mainActivityIndex++;
        var mId = mainData.id || '';
        var mNo = mainData.item_no || '';
        var mName = mainData.item_name || '';
        var mBudget = mainData.allocated_budget !== undefined ? formatMoneyDisplay(mainData.allocated_budget) : '0.00';
        var hasChildren = mainData.children && mainData.children.length > 0;

        var html = '<div class="main-activity-card p-3 rounded-3 border bg-white shadow-xs mb-2" data-main-index="' + mIdx + '" style="border-color: #e2e8f0;">' +
            '<input type="hidden" name="sub_items[' + mIdx + '][id]" value="' + mId + '" class="main-id">' +
            '<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">' +
                '<div class="d-flex align-items-center gap-2">' +
                    '<span class="badge bg-primary px-2 py-1 rounded-pill fs-8 fw-bold">' +
                        '<i class="bx bx-layer me-1"></i>กิจกรรมหลัก' +
                    '</span>' +
                    '<span class="badge bg-label-info rounded-pill fs-8 child-counter-badge ' + (hasChildren ? '' : 'd-none') + '">' +
                        'รวมจาก <span class="child-count">' + (hasChildren ? mainData.children.length : 0) + '</span> กิจกรรมย่อย' +
                    '</span>' +
                '</div>' +
                '<div class="d-flex align-items-center gap-1">' +
                    '<button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2 btn-add-child-activity">' +
                        '<i class="bx bx-plus me-1"></i> เพิ่มกิจกรรมย่อย (10.1.2)' +
                    '</button>' +
                    '<button type="button" class="btn btn-xs btn-icon btn-outline-danger rounded-circle btn-remove-main-activity" title="ลบกิจกรรมหลักนี้">' +
                        '<i class="bx bx-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +

            '<div class="d-flex align-items-center gap-2 flex-nowrap">' +
                '<div style="width: 120px; flex-shrink: 0;">' +
                    '<div class="input-group input-group-sm">' +
                        '<span class="input-group-text bg-light text-muted fw-semibold py-0 px-1.5" style="font-size: 0.76rem;">รหัส</span>' +
                        '<input type="text" class="form-control form-control-sm main-item-no fw-bold" style="font-size: 0.82rem;" name="sub_items[' + mIdx + '][item_no]" value="' + (mNo || '') + '" placeholder="เช่น 10.1">' +
                    '</div>' +
                '</div>' +
                '<div class="flex-grow-1" style="min-width: 0;">' +
                    '<input type="text" class="form-control form-control-sm main-item-name fw-semibold text-dark" style="font-size: 0.84rem;" name="sub_items[' + mIdx + '][item_name]" value="' + (mName || '') + '" placeholder="ชื่อกิจกรรมหลัก เช่น กิจกรรมวันสำคัญแห่งชาติ">' +
                '</div>' +
                '<div style="width: 160px; flex-shrink: 0;">' +
                    '<div class="input-group input-group-sm">' +
                        '<span class="input-group-text bg-light text-muted py-0 px-1.5" style="font-size: 0.76rem;">งบ (฿)</span>' +
                        '<input type="text" class="form-control form-control-sm text-end input-money main-item-budget fw-bold ' + (hasChildren ? 'bg-light text-muted' : 'text-success') + '" style="font-size: 0.82rem;" name="sub_items[' + mIdx + '][allocated_budget]" value="' + mBudget + '" ' + (hasChildren ? 'readonly' : '') + ' placeholder="0.00">' +
                    '</div>' +
                '</div>' +
            '</div>' +

            '<div class="child-activities-wrapper mt-3 ps-3 border-start border-3 border-info ' + (hasChildren ? '' : 'd-none') + '">' +
                '<div class="d-flex align-items-center justify-content-between mb-2">' +
                    '<small class="text-muted fw-semibold" style="font-size: 0.78rem;"><i class="bx bx-subdirectory-right text-info me-1"></i>รายการกิจกรรมย่อยภายใต้กิจกรรมหลักนี้:</small>' +
                '</div>' +
                '<div class="child-activities-container d-flex flex-column gap-2">' +
                '</div>' +
            '</div>' +
        '</div>';

        var $card = $(html);
        $('#mainActivitiesContainer').append($card);
        applyInputMask($card.find('.input-money'));

        if (hasChildren) {
            $.each(mainData.children, function(cIdx, child) {
                renderChildActivity($card, child);
            });
        }

        updateOverallSubItemsCalculation();
        return $card;
    }

    function renderChildActivity($mainCard, childData) {
        childData = childData || {};
        var mIdx = $mainCard.data('main-index');
        var cIdx = $mainCard.find('.child-activity-row').length;
        var cId = childData.id || '';
        var cNo = childData.item_no || '';
        var cName = childData.item_name || '';
        var cBudget = childData.allocated_budget !== undefined ? formatMoneyDisplay(childData.allocated_budget) : '0.00';

        if (!cNo) {
            var parentNo = $.trim($mainCard.find('.main-item-no').val());
            if (parentNo) {
                cNo = parentNo + '.' + (cIdx + 1);
            }
        }

        var html = '<div class="child-activity-row p-1.5 px-2 rounded-2 border bg-light d-flex align-items-center gap-2 flex-nowrap" data-child-index="' + cIdx + '">' +
            '<input type="hidden" name="sub_items[' + mIdx + '][children][' + cIdx + '][id]" value="' + cId + '" class="child-id">' +
            '<div style="width: 110px; flex-shrink: 0;">' +
                '<div class="input-group input-group-sm">' +
                    '<span class="input-group-text bg-white text-info py-0 px-1.5"><i class="bx bx-subdirectory-right"></i></span>' +
                    '<input type="text" class="form-control form-control-sm child-item-no fw-semibold" style="font-size: 0.80rem;" name="sub_items[' + mIdx + '][children][' + cIdx + '][item_no]" value="' + (cNo || '') + '" placeholder="เช่น 10.1.2">' +
                '</div>' +
            '</div>' +
            '<div class="flex-grow-1" style="min-width: 0;">' +
                '<input type="text" class="form-control form-control-sm child-item-name" style="font-size: 0.82rem;" name="sub_items[' + mIdx + '][children][' + cIdx + '][item_name]" value="' + (cName || '') + '" placeholder="ชื่อกิจกรรมย่อย เช่น กิจกรรมวันเฉลิมพระชนมพรรษา...">' +
            '</div>' +
            '<div style="width: 150px; flex-shrink: 0;">' +
                '<div class="input-group input-group-sm">' +
                    '<span class="input-group-text bg-white text-muted py-0 px-1.5" style="font-size: 0.74rem;">งบ (฿)</span>' +
                    '<input type="text" class="form-control form-control-sm text-end input-money child-item-budget fw-bold text-success" style="font-size: 0.82rem;" name="sub_items[' + mIdx + '][children][' + cIdx + '][allocated_budget]" value="' + cBudget + '" placeholder="0.00">' +
                '</div>' +
            '</div>' +
            '<div style="flex-shrink: 0;">' +
                '<button type="button" class="btn btn-xs btn-icon btn-outline-danger rounded-circle btn-remove-child-activity" title="ลบกิจกรรมย่อยนี้">' +
                    '<i class="bx bx-trash"></i>' +
                '</button>' +
            '</div>' +
        '</div>';

        var $row = $(html);
        var $wrapper = $mainCard.find('.child-activities-wrapper');
        var $container = $mainCard.find('.child-activities-container');
        $wrapper.removeClass('d-none');
        $container.append($row);
        applyInputMask($row.find('.input-money'));

        recalculateMainCard($mainCard);
    }

    function recalculateMainCard($mainCard) {
        var $childRows = $mainCard.find('.child-activity-row');
        var count = $childRows.length;
        var $counterBadge = $mainCard.find('.child-counter-badge');
        var $wrapper = $mainCard.find('.child-activities-wrapper');
        var $mainBudgetInput = $mainCard.find('.main-item-budget');

        if (count > 0) {
            $wrapper.removeClass('d-none');
            $counterBadge.removeClass('d-none').find('.child-count').text(count);
            
            var sumChildren = 0.0;
            $childRows.find('.child-item-budget').each(function () {
                sumChildren += parseMoneyValue($(this).val());
            });

            $mainBudgetInput.val(formatMoneyDisplay(sumChildren))
                .prop('readonly', true)
                .addClass('bg-light text-muted')
                .removeClass('text-success');
        } else {
            $wrapper.addClass('d-none');
            $counterBadge.addClass('d-none');
            $mainBudgetInput.prop('readonly', false)
                .removeClass('bg-light text-muted')
                .addClass('text-success');
        }

        updateOverallSubItemsCalculation();
    }

    function updateOverallSubItemsCalculation() {
        var totalCards = $('#mainActivitiesContainer .main-activity-card').length;
        if (totalCards === 0) {
            $('#emptyActivityAlert').show();
            $('#subItemsTotalRow').addClass('d-none');
            $('#allocatedBudgetAutoBadge').addClass('d-none');
            $('#allocatedBudget').prop('readonly', false);
            return;
        }

        $('#emptyActivityAlert').hide();
        $('#subItemsTotalRow').removeClass('d-none');
        $('#allocatedBudgetAutoBadge').removeClass('d-none');

        var grandTotal = 0.0;
        $('#mainActivitiesContainer .main-activity-card').each(function () {
            var $card = $(this);
            var $children = $card.find('.child-activity-row');
            if ($children.length > 0) {
                var sumChildren = 0.0;
                $children.find('.child-item-budget').each(function () {
                    sumChildren += parseMoneyValue($(this).val());
                });
                $card.find('.main-item-budget').val(formatMoneyDisplay(sumChildren));
                grandTotal += sumChildren;
            } else {
                grandTotal += parseMoneyValue($card.find('.main-item-budget').val());
            }
        });

        $('#subItemsTotalDisplay').text(formatMoneyDisplay(grandTotal) + ' บาท');
        $('#allocatedBudget').val(formatMoneyDisplay(grandTotal)).prop('readonly', true);
    }

    $('#btnAddMainActivity').on('click', function () {
        renderMainActivity();
    });

    $(document).on('click', '.btn-add-child-activity', function () {
        var $card = $(this).closest('.main-activity-card');
        renderChildActivity($card);
    });

    $(document).on('click', '.btn-remove-main-activity', function () {
        $(this).closest('.main-activity-card').remove();
        updateOverallSubItemsCalculation();
    });

    $(document).on('click', '.btn-remove-child-activity', function () {
        var $card = $(this).closest('.main-activity-card');
        $(this).closest('.child-activity-row').remove();
        recalculateMainCard($card);
    });

    $(document).on('input keyup change blur', '.child-item-budget', function () {
        var $card = $(this).closest('.main-activity-card');
        recalculateMainCard($card);
    });

    $(document).on('input keyup change blur', '.main-item-budget', function () {
        updateOverallSubItemsCalculation();
    });

    // Edit Project Click
    var pendingProjectData = null;

    function populateProjectModal(data) {
        $('#modalProjectTitle').html('<i class="bx bx-edit text-warning me-2"></i>แก้ไขโครงการ');
        $('#projectId').val(data.id);
        $('#projectYear').val(data.budget_year);
        $('#projectStrategySelect').val(data.strategy_id || '');
        window.loadTacticsForProject(data.strategy_id, data.tactic_id);
        $('#projectCode').val(data.project_code || '');
        $('#projectName').val(data.project_name);
        $('#budgetSource').val(data.budget_source || '');
        
        // Always fully reset activities before populating
        $('#mainActivitiesContainer').empty();
        mainActivityIndex = 0;
        $('#subItemsTotalRow').addClass('d-none');
        $('#allocatedBudgetAutoBadge').addClass('d-none');
        $('#subItemsTotalDisplay').text('0.00 บาท');

        if (data.sub_items && data.sub_items.length > 0) {
            $.each(data.sub_items, function (i, sub) {
                renderMainActivity(sub);
            });
        }
        
        updateOverallSubItemsCalculation();

        // If no activities, set allocated budget directly from project data and allow manual editing
        if (!data.sub_items || data.sub_items.length === 0) {
            $('#allocatedBudget').val(formatMoneyDisplay(data.allocated_budget)).prop('readonly', false);
            $('#subItemsTotalRow').addClass('d-none');
            $('#allocatedBudgetAutoBadge').addClass('d-none');
            $('#emptyActivityAlert').show();
        }
    }

    $(document).on('click', '.btn-edit-project', function () {
        var data = $(this).data('project');
        if (typeof data === 'string') {
            try { data = JSON.parse(data); } catch (e) {}
        }

        var $modal = $('#modalAddProject');
        // If modal is currently open or in closing animation, store data and close first
        // to avoid race condition where hidden.bs.modal wipes the new data
        if ($modal.hasClass('show') || $modal.hasClass('showing') || $('body').hasClass('modal-open')) {
            pendingProjectData = data;
            $modal.modal('hide');
            return;
        }

        populateProjectModal(data);
        $modal.modal('show');
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
        // If there's pending data from clicking edit while modal was closing, 
        // populate with new data and reopen immediately (prevents race condition)
        if (pendingProjectData) {
            var data = pendingProjectData;
            pendingProjectData = null;
            populateProjectModal(data);
            $('#modalAddProject').modal('show');
            return;
        }

        // Normal reset when modal is closed without pending data
        $('#formProject')[0].reset();
        $('#projectId').val('');
        $('#allocatedBudget').val('0.00').prop('readonly', false);
        $('#mainActivitiesContainer').empty();
        mainActivityIndex = 0;
        $('#emptyActivityAlert').show();
        $('#subItemsTotalRow').addClass('d-none');
        $('#allocatedBudgetAutoBadge').addClass('d-none');
        $('#subItemsTotalDisplay').text('0.00 บาท');
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
