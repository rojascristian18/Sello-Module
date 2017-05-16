{extends file="helpers/form/form.tpl"}
{block name="field"}
	{if $input.type == 'file'}
		
		<div class="col-lg-9 ">
			<div class="form-group">
				<div class="col-sm-6">
					{if isset($url_image)}<img src="{$url_image}" class="img-responsive">{/if}
					<input id="MODULO_SELLO_IMG" name="MODULO_SELLO_IMG" class="hide" type="file">
					<div class="dummyfile input-group">
						<span class="input-group-addon"><i class="icon-file"></i></span>
						<input id="MODULO_SELLO_IMG-name" name="filename" readonly="" type="text">
						<span class="input-group-btn">
							<button id="MODULO_SELLO_IMG-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
								<i class="icon-folder-open"></i> Añadir archivo</button>
						</span>
					</div>
				</div>
		</div>
			<script type="text/javascript">

				$(document).ready(function(){
					$('#MODULO_SELLO_IMG-selectbutton').click(function(e) {
						$('#MODULO_SELLO_IMG').trigger('click');
					});

					$('#MODULO_SELLO_IMG-name').click(function(e) {
						$('#MODULO_SELLO_IMG').trigger('click');
					});

					$('#MODULO_SELLO_IMG-name').on('dragenter', function(e) {
						e.stopPropagation();
						e.preventDefault();
					});

					$('#MODULO_SELLO_IMG-name').on('dragover', function(e) {
						e.stopPropagation();
						e.preventDefault();
					});

					$('#MODULO_SELLO_IMG-name').on('drop', function(e) {
						e.preventDefault();
						var files = e.originalEvent.dataTransfer.files;
						$('#MODULO_SELLO_IMG')[0].files = files;
						$(this).val(files[0].name);
					});

					$('#MODULO_SELLO_IMG').change(function(e) {
						if ($(this)[0].files !== undefined)
						{
							var files = $(this)[0].files;
							var name  = '';

							$.each(files, function(index, value) {
								name += value.name+', ';
							});

							$('#MODULO_SELLO_IMG-name').val(name.slice(0, -2));
						}
						else // Internet Explorer 9 Compatibility
						{
							var name = $(this).val().split(/[\\/]/);
							$('#MODULO_SELLO_IMG-name').val(name[name.length-1]);
						}
					});

					if (typeof MODULO_SELLO_IMG_max_files !== 'undefined')
					{
						$('#MODULO_SELLO_IMG').closest('form').on('submit', function(e) {
							if ($('#MODULO_SELLO_IMG')[0].files.length > MODULO_SELLO_IMG_max_files) {
								e.preventDefault();
								alert('You can upload a maximum of  files');
							}
						});
					}
				});
			</script>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
	
{/block}