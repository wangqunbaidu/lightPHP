<?php
/**
 * 目前继承于spl中的fileinfo 该类只读
 * 有些不合理 因为view也直接用到了这个类 暂定
 */
class Light_File_Info extends SplFileInfo {
    public function __construct( $filename, $ext = '' ){
        if ( !$this->getExtension( $filename ) ) {
            $filename .=  $ext;
        }

        parent::__construct( $filename );
    }
    
    /**
     * 获取文件的后缀
     *
     * @return unknown
     */
    public function getExtension( $filename = '' ){
        $filename = $filename ? $filename : $this->getFilename();
        
        $info = pathinfo( $filename );
        
        return $info['extension'];
    }
    
    /**
     * 获取文件内容
     *
     * @return unknown
     */
    public function getFileContent(){
        return file_get_contents( $this->getPathname() );
    }
}
?>