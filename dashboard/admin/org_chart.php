<?php
declare(strict_types=1);

$pageTitle = "Interactive Org Chart";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();

// 1. Fetch data
$companies = $pdo->query("SELECT * FROM companies")->fetchAll(PDO::FETCH_ASSOC);
$branches = $pdo->query("SELECT * FROM branches")->fetchAll(PDO::FETCH_ASSOC);
$departments = $pdo->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);
$designations = $pdo->query("SELECT * FROM designations")->fetchAll(PDO::FETCH_ASSOC);

// Employees with profile details
$employeesStmt = $pdo->query("
    SELECT e.*, p.full_name, p.profile_photo 
    FROM employees e 
    JOIN employee_profiles p ON e.id = p.employee_id 
    WHERE e.employment_status = 'Active'
");
$employees = $employeesStmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Helper to group items by parent
function getChildren($items, $parentIdKey, $parentId) {
    return array_filter($items, function($item) use ($parentIdKey, $parentId) {
        return $item[$parentIdKey] == $parentId;
    });
}
?>

<div class="org-chart-wrapper">
    <div class="card-custom bg-white p-4">
        <h5 class="text-primary mb-4 text-center">Organizational Directory</h5>
        
        <div class="org-tree">
            <ul>
                <?php foreach ($companies as $comp): ?>
                <li>
                    <div class="org-node node-company">
                        <div class="node-icon"><i class="fa-solid fa-building"></i></div>
                        <div class="node-title"><?= htmlspecialchars($comp['name']) ?></div>
                        <div class="node-subtitle">Company</div>
                    </div>
                    <?php 
                    $compBranches = getChildren($branches, 'company_id', $comp['id']); 
                    if (count($compBranches) > 0): 
                    ?>
                    <ul>
                        <?php foreach ($compBranches as $branch): ?>
                        <li>
                            <div class="org-node node-branch">
                                <div class="node-icon"><i class="fa-solid fa-code-branch"></i></div>
                                <div class="node-title"><?= htmlspecialchars($branch['name']) ?></div>
                                <div class="node-subtitle">Branch</div>
                            </div>
                            <?php 
                            $branchDepts = getChildren($departments, 'branch_id', $branch['id']);
                            if (count($branchDepts) > 0):
                            ?>
                            <ul>
                                <?php foreach ($branchDepts as $dept): ?>
                                <li>
                                    <div class="org-node node-department">
                                        <div class="node-icon"><i class="fa-solid fa-users"></i></div>
                                        <div class="node-title"><?= htmlspecialchars($dept['name']) ?></div>
                                        <div class="node-subtitle">Department</div>
                                    </div>
                                    <?php 
                                    $deptDesigs = getChildren($designations, 'department_id', $dept['id']);
                                    if (count($deptDesigs) > 0):
                                    ?>
                                    <ul>
                                        <?php foreach ($deptDesigs as $desig): ?>
                                        <li>
                                            <div class="org-node node-designation">
                                                <div class="node-title"><?= htmlspecialchars($desig['title']) ?></div>
                                            </div>
                                            <?php 
                                            $desigEmps = getChildren($employees, 'designation_id', $desig['id']);
                                            if (count($desigEmps) > 0):
                                            ?>
                                            <ul>
                                                <?php foreach ($desigEmps as $emp): ?>
                                                <li>
                                                    <div class="org-node node-employee">
                                                        <div class="emp-photo">
                                                            <?php if (!empty($emp['profile_photo'])): ?>
                                                                <img src="<?= get_base_url() . '/' . $emp['profile_photo'] ?>" alt="Photo">
                                                            <?php else: ?>
                                                                <div class="emp-initials"><?= strtoupper(substr($emp['full_name'], 0, 2)) ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="node-title"><?= htmlspecialchars($emp['full_name']) ?></div>
                                                        <div class="node-subtitle text-muted"><?= htmlspecialchars($emp['employee_custom_id']) ?></div>
                                                    </div>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php endif; ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<style>
/* CSS Tree Structure */
.org-chart-wrapper {
    overflow-x: auto;
    padding-bottom: 30px;
}
.org-tree {
    display: flex;
    justify-content: center;
    min-width: min-content;
}
.org-tree ul {
    padding-top: 20px;
    position: relative;
    transition: all 0.5s;
    display: flex;
    justify-content: center;
    padding-left: 0;
}
.org-tree li {
    float: left;
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 10px 0 10px;
    transition: all 0.5s;
}
.org-tree li::before, .org-tree li::after{
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 2px solid #b3b3b3;
    width: 50%;
    height: 20px;
}
.org-tree li::after{
    right: auto;
    left: 50%;
    border-left: 2px solid #b3b3b3;
}
.org-tree li:only-child::after, .org-tree li:only-child::before {
    display: none;
}
.org-tree li:only-child{
    padding-top: 0;
}
.org-tree li:first-child::before, .org-tree li:last-child::after{
    border: 0 none;
}
.org-tree li:first-child::after{
    border-radius: 5px 0 0 0;
}
.org-tree li:last-child::before{
    border-right: 2px solid #b3b3b3;
    border-radius: 0 5px 0 0;
}
.org-tree li ul::before{
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 2px solid #b3b3b3;
    width: 0;
    height: 20px;
    transform: translateX(-50%);
}

/* Node Styling */
.org-node {
    display: inline-block;
    padding: 15px 20px;
    text-align: center;
    border-radius: 8px;
    background: #fff;
    border: 2px solid #e0e0e0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    min-width: 150px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}
.org-node:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    border-color: var(--primary-color);
}
.node-company {
    border-color: #2b3990;
    border-top-width: 4px;
}
.node-branch {
    border-color: #0b2545;
    border-top-width: 4px;
}
.node-department {
    border-color: #a98f3b;
    border-top-width: 4px;
}
.node-designation {
    border-color: #6c757d;
    padding: 8px 15px;
    min-width: auto;
    background: #f8f9fa;
}
.node-employee {
    border-color: #28a745;
    border-top-width: 4px;
    min-width: 160px;
}

.node-icon {
    font-size: 24px;
    color: #a98f3b;
    margin-bottom: 8px;
}
.node-title {
    font-weight: 700;
    color: #333;
    font-size: 14px;
    margin-bottom: 4px;
}
.node-subtitle {
    font-size: 11px;
    color: #777;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.emp-photo {
    width: 50px;
    height: 50px;
    margin: 0 auto 10px auto;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #28a745;
}
.emp-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.emp-initials {
    width: 100%;
    height: 100%;
    background-color: #28a745;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 20px;
}
</style>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
