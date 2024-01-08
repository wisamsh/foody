(function () {
  try {
    if (
      typeof _peq === "undefined" ||
      typeof pushengageCategorySegment === "undefined" ||
      typeof pushengageCategorySegment.addSegment !== "object"
    ) {
      return;
    }

    // read the existing segment from local storage
    var existingPushSegments = {};
    try {
      existingPushSegments = JSON.parse(localStorage.getItem("PushSegments")) || {};
    } catch (e) {}

    // find the new segments
    var newSegments = [];
    for (var segmentId in pushengageCategorySegment.addSegment) {
      if (!existingPushSegments[segmentId]) {
        var segmentName = pushengageCategorySegment.addSegment[segmentId];
        newSegments.push(segmentName);
        existingPushSegments[segmentId] = segmentName;
      }
    }

    // update the user segments
    if (newSegments.length) {
      window._peq.push([
        "add-to-segment",
        newSegments,
        function (res) {
          if (res.statuscode === 1 || res.statuscode === 2) {
            if (typeof _pe !== 'undefined') {
              _pe.addSegmentsInStorage(existingPushSegments);
            }
          }
        },
      ]);
    }
  } catch (e) {
    console.error(e);
  }
})();
