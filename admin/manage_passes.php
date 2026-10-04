<?php
// ================= ADMIN AUTHENTICATION =================
session_start();
require_once "../config.php";

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../admin_login.php");
    exit;
}

// ================= ACTION LOGIC =================
if (isset($_GET['action_id']) && isset($_GET['new_status'])) {

    $id = intval($_GET['action_id']);
    $status = mysqli_real_escape_string($conn, $_GET['new_status']);

    // Allow only valid statuses
    if ($status !== 'Approved' && $status !== 'Rejected') {
        header("Location: manage_passes.php");
        exit;
    }

    // expiry_date is NOT present in applications table
    if ($status === 'Approved') {
        $sql = "UPDATE applications
                SET status = 'Approved'
                WHERE id = $id";
    } else {
        $sql = "UPDATE applications
                SET status = 'Rejected'
                WHERE id = $id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_passes.php?msg=updated");
        exit;
    }
}

// ================= FETCH APPLICATIONS =================
$query = "SELECT * FROM applications ORDER BY id DESC";
$result = mysqli_query($conn, $query);


// ================= DOCUMENT URL HELPER =================
// If Cloudinary URL is stored, use it directly.
// Otherwise use old uploads folder path.
function documentUrl($file)
{
    if (empty($file)) {
        return '';
    }

    // Cloudinary / external URL
    if (filter_var($file, FILTER_VALIDATE_URL)) {
        return $file;
    }

    // Old local upload filename
    return "../uploads/" . rawurlencode(basename($file));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Manage Applications | SmartPass Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    >

    <style>

        body {
            background: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }

        .wrapper {
            max-width: 1250px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .glass-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .table thead th {
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 20px;
        }

        .table tbody td {
            padding: 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .doc-btn {
            padding: 8px 12px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: 0.2s;
        }

        .doc-btn:hover {
            background: #6366f1;
            color: #fff;
            border-color: #6366f1;
        }

        .status-pill {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .st-approved {
            background: #dcfce7;
            color: #166534;
        }

        .st-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .st-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .modal-header {
            background: #0f172a;
            color: white;
        }

        .viewer-frame {
            width: 100%;
            height: 70vh;
            border: none;
        }

    </style>

</head>

<body>

<div class="wrapper">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="fw-bold text-dark">
            Application Management
        </h3>

        <a
            href="dashboard.php"
            class="btn btn-outline-dark rounded-pill px-4"
        >
            <i class="fa fa-chevron-left me-2"></i>
            Back to Dashboard
        </a>

    </div>


    <div class="glass-card">

        <div class="table-responsive">

            <table class="table mb-0">

                <thead>

                    <tr>

                        <th class="ps-4">
                            Student Info
                        </th>

                        <th class="text-center">
                            Verify Documents
                        </th>

                        <th>
                            Route Details
                        </th>

                        <th>
                            Current Status
                        </th>

                        <th class="text-end pe-4">
                            Decisions
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php while($row = mysqli_fetch_assoc($result)): ?>

                    <?php
                    $idCardUrl = documentUrl($row['id_card_doc']);
                    $aadharUrl = documentUrl($row['aadhar_doc']);
                    $bonafideUrl = documentUrl($row['bonafide_doc']);
                    ?>

                    <tr>

                        <!-- Student Info -->

                        <td class="ps-4">

                            <div class="fw-bold">
                                <?= htmlspecialchars($row['full_name']) ?>
                            </div>

                            <div class="text-muted small">
                                <?= htmlspecialchars($row['college']) ?>
                            </div>

                        </td>


                        <!-- Documents -->

                        <td class="text-center">

                            <div class="d-flex justify-content-center gap-2">

                                <?php if (!empty($idCardUrl)): ?>

                                    <button
                                        onclick="viewDoc(
                                            '<?= htmlspecialchars($idCardUrl, ENT_QUOTES) ?>',
                                            'ID Card'
                                        )"
                                        class="doc-btn"
                                        title="View ID Card"
                                    >
                                        <i class="fa fa-id-card"></i>
                                    </button>

                                <?php endif; ?>


                                <?php if (!empty($aadharUrl)): ?>

                                    <button
                                        onclick="viewDoc(
                                            '<?= htmlspecialchars($aadharUrl, ENT_QUOTES) ?>',
                                            'Aadhar'
                                        )"
                                        class="doc-btn"
                                        title="View Aadhar"
                                    >
                                        <i class="fa fa-address-card"></i>
                                    </button>

                                <?php endif; ?>


                                <?php if (!empty($bonafideUrl)): ?>

                                    <button
                                        onclick="viewDoc(
                                            '<?= htmlspecialchars($bonafideUrl, ENT_QUOTES) ?>',
                                            'Bonafide'
                                        )"
                                        class="doc-btn"
                                        title="View Bonafide"
                                    >
                                        <i class="fa fa-file-contract"></i>
                                    </button>

                                <?php endif; ?>

                            </div>

                        </td>


                        <!-- Route -->

                        <td class="small fw-semibold text-primary">

                            <?= htmlspecialchars($row['route_from']) ?>

                            <i class="fa fa-arrow-right mx-1 text-muted"></i>

                            <?= htmlspecialchars($row['route_to']) ?>

                        </td>


                        <!-- Status -->

                        <td>

                            <span class="status-pill st-<?= strtolower($row['status']) ?>">

                                <?= htmlspecialchars($row['status']) ?>

                            </span>

                        </td>


                        <!-- Decisions -->

                        <td class="text-end pe-4">

                            <?php if($row['status'] == 'Pending'): ?>

                                <a
                                    href="manage_passes.php?action_id=<?= $row['id'] ?>&new_status=Approved"
                                    class="btn btn-sm btn-success rounded-pill px-3 me-1"
                                >
                                    Approve
                                </a>

                                <a
                                    href="manage_passes.php?action_id=<?= $row['id'] ?>&new_status=Rejected"
                                    class="btn btn-sm btn-danger rounded-pill px-3"
                                    onclick="return confirm('Reject this application?')"
                                >
                                    Reject
                                </a>

                            <?php else: ?>

                                <span class="text-muted small italic">
                                    Action Taken
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>


                <?php if(mysqli_num_rows($result) == 0): ?>

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-4"
                        >
                            No applications found
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- Document Viewer Modal -->

<div
    class="modal fade"
    id="viewerModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content border-0">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="docTitle"
                >
                    Document View
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body p-0">

                <iframe
                    src=""
                    id="viewerFrame"
                    class="viewer-frame"
                ></iframe>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary rounded-pill px-4"
                    data-bs-dismiss="modal"
                >
                    <i class="fa fa-times me-2"></i>
                    Close Viewer
                </button>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

function viewDoc(url, title) {

    if (!url) {
        alert("Document is not available.");
        return;
    }

    document.getElementById('viewerFrame').src = url;

    document.getElementById('docTitle').innerText =
        "Reviewing: " + title;

    new bootstrap.Modal(
        document.getElementById('viewerModal')
    ).show();
}

</script>

</body>
</html>
