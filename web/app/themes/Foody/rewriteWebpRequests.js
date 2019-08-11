'use strict';

const webpExtension = 'webp';

const AWS = require('aws-sdk');

const s3 = new AWS.S3();

exports.handler = async (event, context, callback) => {
    const request = event.Records[0].cf.request;
    const headers = request.headers;

    // fetch the uri of original image
    let fwdUri = request.uri;

    // read the accept header to determine if webP is supported.
    let accept = headers['accept'] ? headers['accept'][0].value : "";

    // check support for webp
    if (accept.includes(webpExtension)) {
        fwdUri += "." + webpExtension;
    }

    let key = decodeURI(fwdUri.substr(1));

    console.log('key: ', key);

    let params = {Bucket: 'foody-media', Key: key};
    try {
        await s3.getObject(params).promise();
        request.uri = fwdUri;
        console.log('final uri: ', fwdUri);
    } catch (e) {
        console.log('error: ', e);
    }


    return request;
};