// convert file to image


// handle mobile image orientation
function orientation(img, canvas) {

    // Set variables
    let ctx = canvas.getContext("2d");
    let exifOrientation = '';
    let width = img.width,
        height = img.height;

    console.log(width);
    console.log(height);

    // Check orientation in EXIF metadatas
    EXIF.getData(img, function () {
        let allMetaData = EXIF.getAllTags(this);
        exifOrientation = allMetaData.Orientation;
        console.log('Exif orientation: ' + exifOrientation);
    });

    // set proper canvas dimensions before transform & export
    if (jQuery.inArray(exifOrientation, [5, 6, 7, 8]) > -1) {
        //noinspection JSSuspiciousNameCombination
        canvas.width = height;
        //noinspection JSSuspiciousNameCombination
        canvas.height = width;
    } else {
        canvas.width = width;
        canvas.height = height;
    }

    // transform context before drawing image
    switch (exifOrientation) {
        case 2:
            ctx.transform(-1, 0, 0, 1, width, 0);
            break;
        case 3:
            ctx.transform(-1, 0, 0, -1, width, height);
            break;
        case 4:
            ctx.transform(1, 0, 0, -1, 0, height);
            break;
        case 5:
            ctx.transform(0, 1, 1, 0, 0, 0);
            break;
        case 6:
            ctx.transform(0, 1, -1, 0, height, 0);
            break;
        case 7:
            ctx.transform(0, -1, -1, 0, height, width);
            break;
        case 8:
            ctx.transform(0, -1, 1, 0, 0, width);
            break;
        default:
            ctx.transform(1, 0, 0, 1, 0, 0);
    }

    // Draw img into canvas
    ctx.drawImage(img, 0, 0, width, height);

    $(img).attr('src', canvas.toDataURL());
    img = null;
    canvas = null;
}

module.exports = function (input, img,cb) {

    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $(img).attr('src', e.target.result);
            if(cb && typeof cb === 'function'){
                cb();
            }
        };

        // reader.onloadend = function (e) {
        //
        //     // Update an image tag with loaded image source
        //     $(img).attr('src', e.target.result);
        //     // Use EXIF library to handle the loaded image exif orientation
        //     EXIF.getData(input.files[0], function () {
        //
        //         // // Fetch image tag
        //         // let img = $(img).get(0);
        //         // Fetch canvas
        //         let canvas = document.createElement('canvas');
        //         // run orientation on img in canvas
        //         orientation(img[0], canvas);
        //     });
        // };

        reader.readAsDataURL(input.files[0]);

    }
};