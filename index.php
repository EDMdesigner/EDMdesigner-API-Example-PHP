<?php
if ($_POST["userId"]) {
	$publicId = "TESTAPIKEY";
	$magic = "XSDE422RSDJQDJW8QADM31SMA";

	$ip = $_SERVER["REMOTE_ADDR"];
	$timestamp = time();

	$hash = md5($publicId . $ip . $timestamp . $magic);

	$url = "http://localhost:3000/api/token";

	$data = array(
				"id"	=> $publicId,
				"uid"	=> $_POST["userId"],
				"ip"	=> $ip,
				"ts"	=> $timestamp,
				"hash"	=> $hash
	);

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data),
	    ),
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	print($result);
} else {
?>
<html>
	<head>
		<title>EDMdesigner-API-Example-PHP</title>
		<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
		<script src="//localhost:3000/EDMdesignerAPI.js?route=index.php"></script>
		<script>
			initEDMdesignerPlugin("pluginTest", function(edmPlugin) {
				function updateProjectList() {
					$("#NewProject").hide();
					$("#OpenedProject").hide();
					$("#ProjectList").show();

					var projectListContainer = $("#ProjectListContent")
						.empty();

					edmPlugin.listProjects(function(result) {
						for(var idx = 0; idx < result.length; idx += 1) {
							projectListContainer.append(createProjectListElem(result[idx]));
						}
					});
				}

				function createProjectListElem(data) {
					var elem = $("<div class='project-list-elem'/>");
					var titleAndDescription = $("<div class='info'/>").appendTo(elem);
					var buttons = $("<div class='buttons'/>").appendTo(elem);

					titleAndDescription
						.append($("<h3/>").text(data.title))
						.append($("<p/>").text(data.description));

					var openButton = $("<button/>")
						.text("Open")
						.click(function() {
							edmPlugin.openProject(data._id, function (result) {
								openEditor(result.iframe);
							});
						})
						.appendTo(elem);

					var deleteButton = $("<button/>")
						.text("Delete")
						.click(function() {
							edmPlugin.removeProject(data._id, updateProjectList);
						})
						.appendTo(elem);

					var duplicateButton = $("<button/>")
						.text("Duplicate")
						.click(function() {
							edmPlugin.duplicateProject(data._id, updateProjectList);
						})
						.appendTo(elem);

					return elem;
				}

				function openEditor(iframe) {
					var projectListContainer = $("#ProjectList")
						.hide();

					var closeDiv = $("<div/>");
					var closeButton = $("<button/>")
						.text("Close")
						.click(updateProjectList)
						.appendTo(closeDiv);

					var openedProjectContainer = $("#OpenedProject")
						.empty()
						.append(closeDiv)
						.append(iframe)
						.show();
				}

				$("#NewProjectButton").click(function() {
					$("#ProjectList").hide();
					$("#NewProject").show();
				});

				$("#NewProjectAddButton").click(function() {
					var titleInput = $("#NewProjectTitle"),
						descrInput = $("#NewProjectDescription");

					var data = {
						title: titleInput.val(),
						description: descrInput.val()
					};

					titleInput.val("");
					descrInput.val("");

					edmPlugin.createProject(data, updateProjectList);
				});


				$(document).ready(updateProjectList);
			}, function(error) {
				alert(error);
			});
		</script>
	</head>
	<body>
		<div>
			<h1>EDMdesigner-API-Example-PHP</h1>
		</div>

		<div id="ProjectList">
			<button id="NewProjectButton">New project</button>
			<h2>Projects</h2>

			<div id="ProjectListContent">
			</div>
		</div>

		<div id="OpenedProject">
			<!-- An iframe will be inserted here -->
		</div>

		<div id="NewProject">
			<h2>New project</h2>
			<h3>Title</h3>
			<input id="NewProjectTitle"/>
			<h3>Description</h3>
			<textarea id="NewProjectDescription"></textarea>
			<div>
				<button id="NewProjectAddButton">Add</button>
			</div>
		</div>
	</body>
</html>

<?php
}
?>