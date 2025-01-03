<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
require_once '../config.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once '../components/header.php'; ?>
    <title>ปฏิทินบันทึกงาน</title>
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css' rel='stylesheet' />
    <style>
        .fc-event {
            cursor: pointer;
        }
        .calendar-container {
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <?php require_once '../components/navbar.php'; ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once '../components/sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="calendar-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>ปฏิทินบันทึกงาน</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal">
                            <i class="bi bi-plus-circle"></i> เพิ่มงาน
                        </button>
                    </div>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding/editing events -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">เพิ่มงาน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <input type="hidden" id="eventId" name="id">
                        <div class="mb-3">
                            <label for="title" class="form-label">หัวข้อ</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">รายละเอียด</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">วันที่เริ่ม</label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">สี</label>
                            <input type="color" class="form-control" id="color" name="color" value="#3788d8">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger" id="deleteEvent" style="display: none;">ลบ</button>
                    <button type="button" class="btn btn-primary" id="saveEvent">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ประกาศตัวแปร Modal
            const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
            const eventForm = document.getElementById('eventForm');
            const saveButton = document.getElementById('saveEvent');
            const deleteButton = document.getElementById('deleteEvent');

            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'th',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                events: 'get_events.php',
                editable: true,
                selectable: true,
                select: function(info) {
                    newEvent(info.startStr);
                },
                eventClick: function(info) {
                    editEvent(info.event);
                }
            });
            calendar.render();

            // ฟังก์ชันสำหรับเพิ่มอีเวนท์ใหม่
            function newEvent(dateStr) {
                eventForm.reset();
                document.getElementById('eventId').value = '';
                document.getElementById('start_date').value = dateStr.slice(0, 16);
                document.getElementById('end_date').value = dateStr.slice(0, 16);
                document.getElementById('deleteEvent').style.display = 'none';
                document.getElementById('modalTitle').textContent = 'เพิ่มงาน';
                eventModal.show();
            }

            // ฟังก์ชันสำหรับแก้ไขอีเวนท์
            function editEvent(event) {
                document.getElementById('eventId').value = event.id;
                document.getElementById('title').value = event.title;
                document.getElementById('description').value = event.extendedProps.description || '';
                document.getElementById('start_date').value = event.start.toISOString().slice(0, 16);
                document.getElementById('end_date').value = event.end ? event.end.toISOString().slice(0, 16) : event.start.toISOString().slice(0, 16);
                document.getElementById('color').value = event.backgroundColor || '#3788d8';
                document.getElementById('deleteEvent').style.display = 'block';
                document.getElementById('modalTitle').textContent = 'แก้ไขงาน';
                eventModal.show();
            }

            // การจัดการการบันทึกข้อมูล
            saveButton.addEventListener('click', function() {
                if (!eventForm.checkValidity()) {
                    eventForm.reportValidity();
                    return;
                }

                const formData = new FormData(eventForm);
                
                fetch('save_event.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกสำเร็จ',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        calendar.refetchEvents();
                        eventModal.hide();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message || 'ไม่สามารถบันทึกข้อมูลได้'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
                    });
                });
            });

            // การจัดการการลบข้อมูล
            deleteButton.addEventListener('click', function() {
                const eventId = document.getElementById('eventId').value;
                if (!eventId) return;

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "คุณไม่สามารถกู้คืนข้อมูลได้!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('delete_event.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ id: eventId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ลบข้อมูลสำเร็จ',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                calendar.refetchEvents();
                                eventModal.hide();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: data.message || 'ไม่สามารถลบข้อมูลได้'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
                            });
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
