(function ($) {
	var fileUploadCount = 0;

	$.fn.fileUpload = function () {
		return this.each(function () {
			var fileUploadDiv = $(this);
			var filePreviewUploadDiv = $("#preview-upload");
			var fileUploadId = `fileUpload-${++fileUploadCount}`;

			// Creates HTML content for the file upload area.
			var fileDivContent = `	
                <label for="${fileUploadId}" class="file-upload mb-0 pb-0">
                    <div>
                        <span>Drag & Drop Files Here</span><br>
                        <span>OR</span>
                        <div>Browse Files</div>
                    </div>
                    <input type="file" id="${fileUploadId}" name=attachments[] multiple hidden />
                </label>
            `;

			fileUploadDiv.html(fileDivContent).addClass("file-container");

			// var table = null;
			// var table = $("#table_preview");
			var tableBody = $("#table_preview").find("tbody");
			// Creates a table containing file information.
			// function createTable() {
			// 	table = $(`
			//         <table class="table table-sm table-hover table-stripped">
			//             <thead class="thead-light">
			//                 <tr>
			//                     <th></th>
			//                     <th style="width: 30%;">File Name</th>
			//                     <th>Preview</th>
			//                     <th style="width: 20%;">Size</th>
			//                     <th>Type</th>
			//                     <th></th>
			//                 </tr>
			//             </thead>
			//             <tbody>
			//             </tbody>
			//         </table>
			//     `);

			// 	tableBody = table.find("tbody");
			// 	filePreviewUploadDiv.append(table);
			// }

			// Adds the information of uploaded files to table.
			function handleFiles(files) {
				// if (!table) {
				// 	createTable();
				// }

				tableBody.empty();
				var max_size = 1024 * 1024 * 5; // 5MB
				if (files.length > 0) {
					$.each(files, function (index, file) {
						var fileName = file.name;
						var fileSize = (file.size / 1024).toFixed(2) + " KB";
						var fileType = file.type;
						var preview = fileType.startsWith("image")
							? `<img src="${URL.createObjectURL(
									file
							  )}" alt="${fileName}" height="30">`
							: `<i class="fa fa-eye-slash" aria-hidden="true"></i>`;

						if (file.size > max_size) {
							toastr.error(
								"File size should not exceed 5MB. Please upload a smaller file."
							);
							return false;
						} else {
							tableBody.append(`
								<tr>
									<td>${index + 1}</td>
									<td>${fileName}</td>
									<td>${preview}</td>
									<td>${fileSize}</td>
									<td>${fileType}</td>
									<td><button type="button" class="btn btn-sm btn-danger deleteBtn"><i class="fa fa-trash" aria-hidden="true"></i></td>
								</tr>
							`);
						}
					});

					tableBody.find(".deleteBtn").click(function () {
						$(this).closest("tr").remove();

						if (tableBody.find("tr").length === 0) {
							tableBody.append(
								'<tr><td colspan="6" class="no-file">No files selected!</td></tr>'
							);
						}
					});
				}
			}

			// Events triggered after dragging files.
			fileUploadDiv.on({
				dragover: function (e) {
					e.preventDefault();
					fileUploadDiv.toggleClass("dragover", e.type === "dragover");
				},
				drop: function (e) {
					e.preventDefault();
					fileUploadDiv.removeClass("dragover");
					handleFiles(e.originalEvent.dataTransfer.files);
				},
			});

			// Event triggered when file is selected.
			fileUploadDiv.find(`#${fileUploadId}`).change(function () {
				handleFiles(this.files);
			});
		});
	};
})(jQuery);
