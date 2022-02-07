<div class="modal fade" id="video_format_info_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $this->lang->line('Instagram Media Restrictions'); ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<h5>Image</h5>
				<ul>
					<li>JPEG is the only image format supported.</li>
					<li> Extended JPEG formats such as MPO and JPS are not supported.</li>
					<li>The image's aspect ratio must falls withing a 4:5 to 1.91:1 range</li>
				</ul>
				<br>
				<h5>Video</h5>
				<ul>
					<li><b>Container:</b> MOV or MP4 (MPEG-4 Part 14), no edit lists, moov atom at the front of the file.</li>
					<li><b>Audio codec:</b> AAC, 48khz sample rate maximum, 1 or 2 channels (mono or stereo).</li>
					<li><b>Video codec:</b> HEVC or H264, progressive scan, closed GOP, 4:2:0 chroma subsampling.</li>
					<li><b>Frame rate:</b> 23-60 FPS.</li>
					<li><b>Picture size:</b>
						<ul>
							<li>Maximum columns (horizontal pixels): 1920</li>
							<li>Minimum aspect ratio [cols / rows]: 4 / 5</li>
							<li>Maximum aspect ratio [cols / rows]: 16 / 9</li>
						</ul>
					</li>
					<li><b>Video bitrate:</b> VBR, 5Mbps maximum</li>
					<li><b>Audio bitrate:</b> 128kbps</li>
					<li><b>Duration:</b> 60 seconds maximum, 3 seconds minimum</li>
					<li><b>File size:</b> 100MB maximum</li>
				</ul>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="response_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $this->lang->line('Campaign Status'); ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert text-center" id="response_modal_content">

				</div>
			</div>
		</div>
	</div>
</div>
