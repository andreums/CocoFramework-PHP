<?php
/**
 *  Andrés Ignacio Martínez Soto
 *  andresmartinezsoto@gmail.com  
 * Coco-PHP
 * License: Copyright (c) 2010-2012 Andrés Ignacio Martínez Soto    
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
    class FW_Util_Image extends FW_Singleton {

        public function resize($path, $fitInWidth, $fitInHeight, $newName = '', $jpegQuality = 100) {
            list($width, $height, $type) = getimagesize($path);
            //Getting image information

            $scaleW = $fitInWidth / $width;
            $scaleH = $fitInHeight / $height;
            if ( $scaleH > $scaleW ) {
                $new_width = $fitInWidth;
                $new_height = floor($height * $scaleW);
            }
            else {
                $new_height = $fitInHeight;
                $new_width = floor($width * $scaleH);
            }
            $new_path = $newName == '' ? $path : dirname($path) . '/' . $newName;

            if ( $type == IMAGETYPE_JPEG ) {
                $image_now = imagecreatefromjpeg($path);
                //Get image from path
                $image_new = imagecreatetruecolor($new_width, $new_height);
                //Create new image from scratch
                //Copy image from path into new image ($image_new) with new sizes
                imagecopyresampled($image_new, $image_now, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_new, $new_path, $jpegQuality);
            }
            else
            if ( $type == IMAGETYPE_GIF ) {
                $image_now = imagecreatefromgif($path);
                $image_new = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($image_new, $image_now, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagegif($image_new, $new_path);
            }
            else
            if ( $type == IMAGETYPE_PNG ) {
                $image_now = imagecreatefrompng($path);
                $image_new = imagecreatetruecolor($new_width, $new_height);
                //Setting black color as transparent because image is png
                imagecolortransparent($image_new, imagecolorallocate($image_new, 0, 0, 0));
                imagecopyresampled($image_new, $image_now, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagepng($image_new, $new_path);
            }
            else {
                //Image type is not jpeg, gif or png.
            }
            imagedestroy($image_now);
            imagedestroy($image_new);
        }

        public function makeWaterMark($fileName) {
            $extension = "";
            $image = null;
            $name = explode('.', $fileName);
            $watermark = imagecreatefrompng('images/watermark.png');

            if ( count($name) > 1 ) {
                $extension = $name [1];
            }

            if ( $extension === "jpg" ) {
                $image = imagecreatefromjpeg($fileName);
            }
            if ( $extension === "png" ) {
                $image = imagecreatefrompng($fileName);
            }
            
            $black = imagecolorallocate($watermark, 0, 0, 0);
            imagecolortransparent($watermark, $black);
                        

            $watermark_width = imagesx($watermark);
            $watermark_height = imagesy($watermark);

            $size = getimagesize($fileName);
            $dest_x = (imagesx($image) - $watermark_width)/2;
            $dest_y = (imagesy($image) - $watermark_height)/2;
            imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 45);
            

            imagejpeg($image, $fileName);
            imagedestroy($image);
            imagedestroy($watermark);

        }

    };
?>