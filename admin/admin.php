<?php
include '../enrollment/connection.php';

if (!isset($pdo)) {
  die("Database connection is not established.");
}

$students = [];
$defaultCourse = 'BSME';

$course = isset($_GET['course']) ? $_GET['course'] : $defaultCourse;

try {
  $sql = 'SELECT * FROM students WHERE preferred_course = :course';
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':course', $course, PDO::PARAM_STR);
  $stmt->execute();
  $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SECSA Dashboard</title>
  <style>
    /* General Styles */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
      display: flex;
      height: 100vh;
      background-color: #f8f9fa;
    }

    /* Sidebar Styles */
    /* Sidebar */
    .sidebar {
      width: 290px;
      background-color: #1c1622;
      color: white;
      display: flex;
      flex-direction: column;
      height: 100vh;
      align-items: center;
      justify-content: space-between;
      position: fixed;
      left: 0;
      top: 0;
      transition: transform 0.3s ease-in-out;
      z-index: 999;
    }

    /* Hamburger Button for Mobile View */
    .hamburger {
      display: none;
      font-size: 30px;
      padding: 10px;
      cursor: pointer;
      position: fixed;
      top: 10px;
      left: 20px;
      z-index: 1001;
      color: white;
    }

    .btn-selected {
      width: 165px;
      background-color: #47395c;
      font-weight: bold;
      color: #fff;
      border: 1px solid #d84315;
      box-shadow: 0px 0px 18px 5px rgb(253 0 0 / 50%);
    }

    .btn-logout {
      width: 100%;
      margin-top: 20px;
      padding: 10px;
      background-color: #ff7043;
      color: white;
      border: none;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      margin-bottom: 20px;
    }

    /* Courses Section */
    .courses-section {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: center;
      margin-bottom: auto;
    }

    .course-title {
      font-size: 18px;
      font-weight: bold;
      color: white;
      text-align: center;
      margin-bottom: 10px;
    }

    .btn-courses {
      margin: 10px 20px;
      padding: 10px;
      background-color: #ff7043;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      display: block;
      width: 80%;
    }

    .btn-courses:hover,
    .btn-logout:hover {
      background-color: #e64a19;
    }


    .btn-logout {
      background-color: #ff7043;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      width: 80%;
    }


    .main-content {
      margin-left: 290px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      background-color: #ffffff;
      transition: margin-left 0.3s ease;
    }

    .dashboard-title {
      text-align: center;
      color: #6c5ce7;
      text-shadow: -1px -1px 0 #ff9800, 1px -1px 0 #ff9800, -1px 1px 0 #ff9800, 1px 1px 0 #ff9800;
      font-size: xx-large;
    }



    .table-container {
      max-height: 800px;
      overflow-y: auto;
      border: 1px solid #ddd;
    }

    .student-table {
      width: 100%;
      border-collapse: collapse;
    }

    .student-table th,
    .student-table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
      
    }

    .student-table th {
      background-color: #8e44ad;
      color: white;
      position: sticky;
      top: 0;
      z-index: 1;
      
    }

    .student-table tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .selected-course-title {
      font-size: 25px;
      font-weight: bold;
      color: #8e44ad;
      margin-bottom: 10px;
    }

    img {
      width: 125px;
      height: 100px;
    }

    .head {
      display: flex;
      align-items: center;
    }

    /* Empty Table Message */
    .no-students {
      text-align: center;
      color: #ff7043;
      font-weight: bold;
    }

    .btn-selected {
      width: 165px;
      background-color: #47395c;
      font-weight: bold;
      color: #fff;
      border: 1px solid #d84315;
      box-shadow: 0px 0px 18px 5px rgb(253 0 0 / 50%);
    }

    .btn-logout {
      width: 100%;
      margin-top: 20px;
      padding: 10px;
      background-color: #ff7043;
      color: white;
      border: none;
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      margin-bottom: 20px;
    }

    .btn-edit,
    .btn-delete {
      padding: 5px 10px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }

    .btn-delete {
      background-color: #e74c3c;
    }

    .btn-edit:hover {
      background-color: #2980b9;
    }

    .btn-delete:hover {
      background-color: #c0392b;
    }

    /* Modal Styles */
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      padding-top: 100px;
      transition: opacity 0.3s ease;
    }

    .modal-content {
      background-color: #ffffff;
      margin: 0 auto;
      padding: 30px;
      border-radius: 10px;
      width: 30%;
      height: fit-content;
      max-width: 600px;
      box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
      animation: slideIn 0.4s ease-out;
    }

    @keyframes slideIn {
      from {
        transform: translateY(-100px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }


    .close {
      color: #e43030;
      font-size: 30px;
      font-weight: bold;
      position: relative;
      left: 95%;
      cursor: pointer;
      ;
    }

    .close:hover,
    .close:focus {
      color: #333;
      text-decoration: none;
    }


    .modal h2 {
      font-size: 24px;
      color: #333;
      margin-bottom: 20px;
      font-weight: 600;
      text-align: center;
    }

    input[type="text"],
    input[type="email"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      box-sizing: border-box;
      transition: all 0.3s ease;
    }

    /* Input Hover and Focus Effects */
    input[type="text"]:hover,
    input[type="email"]:hover {
      border-color: #ff7043;
    }

    input[type="text"]:focus,
    input[type="email"]:focus {
      border-color: #ff7043;
      outline: none;
    }

    /* Submit Button Styling */
    button[type="submit"] {
      width: 100%;
      padding: 12px;
      background-color: #ff7043;
      color: #fff;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 10px;
      transition: all 0.3s ease;
    }

    /* Button Hover Effects */
    button[type="submit"]:hover {
      background-color: #e64a19;
    }
    
    /* Media Query for Mobile (max-width: 768px) */
    @media screen and (max-width: 768px) {

      .sidebar {
        transform: translateX(-100%);
      }


      .hamburger {
        display: block;
        position: fixed;
        top: 10px;
        left: 6px;
        z-index: 1001;
        background-color: rebeccapurple;
      }


      .sidebar.active {
        transform: translateX(0);
      }


      .main-content {
        margin-left: 0px;
        margin-top: 52px;
      }

      .courses-section {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
        margin-top: 60px;
      }
      .table-container {
      max-height: 1000px;
      overflow-y: auto;
      border: 1px solid #ddd;
    }


    }

    @media screen and (min-width: 769px) {
      .hamburger {
        display: none;
      }

      .sidebar {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 290px;
      }

      .courses-section {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
      .table-container {
      max-height: 1000px;
      overflow-y: auto;
      border: 1px solid #ddd;
    }
    }


    /* ---------------------------------------------------------------------------------------------------- */
    /* Table Styles */
    

    /* Adjust for small screens */
    @media (max-width: 600px) {
      .modal-content {
        width: 90%;
        padding: 20px;
      }

      .modal h2 {
        font-size: 22px;
      }

      .close {
        font-size: 25px;
      }
      .table-container {
      max-height: 1000px;
      overflow-y: auto;
      border: 1px solid #ddd;
    }
    }
  </style>
</head>

<body>



  <aside class="sidebar">
    <!-- Hamburger Button for Mobile View -->


    <!-- Courses Section -->
    <div class="courses-section">
      <h1 id="courseID">Courses</h1>
      <button class="btn-courses <?php echo ($course == 'BSME') ? 'btn-selected' : ''; ?>"
        onclick="filterTable('BSME')">BSME</button>
      <button class="btn-courses <?php echo ($course == 'BSEE') ? 'btn-selected' : ''; ?>"
        onclick="filterTable('BSEE')">BSEE</button>
      <button class="btn-courses <?php echo ($course == 'BSCE') ? 'btn-selected' : ''; ?>"
        onclick="filterTable('BSCE')">BSCE</button>
      <button class="btn-courses <?php echo ($course == 'BSARCH') ? 'btn-selected' : ''; ?>"
        onclick="filterTable('BSARCH')">BSARCH</button>
      <button class="btn-courses <?php echo ($course == 'BSIT') ? 'btn-selected' : ''; ?>"
        onclick="filterTable('BSIT')">BSIT</button>
    </div>


    <form action="../index.html" method="POST">
      <button class="btn-logout">Logout</button>
    </form>
  </aside>

  <main class="main-content">
    <!-- Header with School Logo and Title -->
    <div class="hamburger" onclick="toggleMenu()">&#9776;</div>
    <div class="head">

      <img src="../assets/sclogo_V1.png" alt="sc" id="sc">
      <h1 class="dashboard-title">School of Engineering, Computer Studies, and Architecture</h1>
    </div>

    <!-- Dynamic Course Title -->
    <div class="selected-course-title">
      <?php echo strtoupper($course) . " STUDENTS"; ?>
    </div>

    <!-- Student Table -->
    <div class="table-container">
      <table class="student-table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="student-table-body">
          <!-- Dynamic content will be inserted here -->
        </tbody>


        <script>

          function toggleMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
          }

        </script>


        <?php
        if (count($students) > 0) {
          foreach ($students as $row) {
            echo "<tr id='student-" . $row['id'] . "'>
                      <td>" . htmlspecialchars($row['id']) . "</td>
                      <td>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . "</td>
                      <td>" . htmlspecialchars($row['preferred_course']) . "</td>
                      <td>" . htmlspecialchars($row['email']) . "</td>
                      <td>
                        <button onclick='openModal(" . $row['id'] . ")'>
                            <img src='../assets/edit_icon.png' alt='Edit' style='width: 20px; height: 20px; border:none;'>
                        </button>
                        <button onclick='deleteStudent(" . $row['id'] . ")'>
                            <img src='../assets/delete_icon.png' alt='Delete' style='width: 20px; height: 20px;'>
                        </button>
                      </td>
                    </tr>";
          }
        } else {
          echo "<tr><td colspan='5' class='no-students'>No students enrolled</td></tr>";
        }
        ?>
        </tbody>
      </table>
    </div>

  </main>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Edit Student Details</h2>
      <form id="editStudentForm">
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" required><br><br>

        <label for="course">Course:</label>
        <input type="text" id="course" name="course" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <input type="hidden" id="studentId" name="studentId">
        <button type="submit">Update</button>
      </form>
    </div>
  </div>

  <script>
    function filterTable(course) {
      window.location.href = `?course=${course}`;
    }

    // Function to open modal and populate fields
    function openModal(id) {
      let row = document.getElementById('student-' + id);
      let cells = row.getElementsByTagName('td');

      // Populate modal with student details
      document.getElementById('fullname').value = cells[1].innerText;
      document.getElementById('course').value = cells[2].innerText;
      document.getElementById('email').value = cells[3].innerText;
      document.getElementById('studentId').value = id;

      // Display modal
      document.getElementById('editModal').style.display = 'flex';
    }

    // Function to close modal
    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    // Function to handle form submission
    document.getElementById('editStudentForm').onsubmit = function (e) {
      e.preventDefault();

      let id = document.getElementById('studentId').value;
      let fullname = document.getElementById('fullname').value;
      let course = document.getElementById('course').value;
      let email = document.getElementById('email').value;

      // Send AJAX request to update student details
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "update_student.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.send(`id=${id}&firstname=${fullname.split(' ')[0]}&lastname=${fullname.split(' ').slice(1).join(' ')}&course=${course}&email=${email}`);

      xhr.onload = function () {
        let response = JSON.parse(xhr.responseText);
        if (xhr.status == 200 && response.status == 'success') {
          alert("Student updated successfully!");
          closeModal();
          window.location.reload();
        } else {
          alert("Error updating student.");
        }
      };
    };

    // Function to handle deleting a student
    function deleteStudent(id) {
      let confirmation = confirm("Are you sure you want to delete this student?");
      if (confirmation) {
        let row = document.getElementById('student-' + id);
        row.parentNode.removeChild(row);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_student.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`id=${id}`);

        xhr.onload = function () {
          let response = JSON.parse(xhr.responseText);
          if (xhr.status == 200 && response.status == 'success') {
            alert("Student deleted successfully!");
          } else {
            alert("Error deleting student.");
          }
        };
      }
    }
  </script>

</body>

</html>