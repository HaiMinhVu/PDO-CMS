/*
We want to preview images, so we need to register the Image Preview plugin
*/
FilePond.registerPlugin(
	
	// encodes the file as base64 data
  	FilePondPluginFileEncode,
	
	// validates the size of the file
	FilePondPluginFileValidateSize,
	
	// corrects mobile image orientation
	FilePondPluginImageExifOrientation,
	
	// previews dropped images
  	FilePondPluginImagePreview,

  	// type validate
  	FilePondPluginFileValidateType

	// image transform
	//FilePondPluginImageTransform,

	// image transform
	//FilePondPluginImageResize,

	// crops the image to a certain aspect ratio
 	//FilePondPluginImageCrop

);
