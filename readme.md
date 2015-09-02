PHP library for extracting images embedded in HTML to files in the file system. It also provides 
replacing embedded code by URL of the new extracted image. 
For extracting from the database use DatabaseExtractor. Useful if your database size
is limited and you have more images embedded in HTML code, e.g. articles created 
by a WYSIWYG editor. 

##Example
Input HTML code:
<img alt="test-image.png" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAYAAABzJ5OsAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADsQAAA7EB9YPtSQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAADmSURBVGiB7dQxDgFBGIbhd4Q4hEKBSvSiVUskCqVb0OqJ3g3cQsMZ6DQSiYLQqVZBRLZZMmP/rHxPMs1m5p+3mCxkmANgToEDlR/ec2TMKfTQPAAHSsAm9PAXxxCYhB6bCz0wTYq3ongrireieCuKt6J4K3mfw809jFbJ+ypnBg1o+dwFTB2s3z94xZeu0N1+tLX+XD4W8Q+ZfjaKt6J4K4+/TZELN2bfHq6eqQGd0FGpiKAXQZTS6sfvz/SzUbwVxVtRvBXFW1G8FcVbUbwVxVvJdLzzORxBGWgHakmydLBL6S75X3fDl01iE28P4AAAAABJRU5ErkJggg=="/>

Output:
<img alt="test-image.png" src="/images/2015/09/55e6e2a0a7836.png"/>
and in the file system a new image file: /target-path/images/2015/09/55e6e2a0a7836.png