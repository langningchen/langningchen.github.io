<?php
    require_once "Function.php";
    $Connect->query("DROP DATABASE IF EXISTS " . $DataBaseName);
    $Connect->query("CREATE DATABASE " . $DataBaseName);
    $Connect->query("USE " . $DataBaseName);
                                                 $Connect->query("CREATE TABLE chatlist                     (
        ID                         int(11)   NOT NULL                               ,
        UID                        int(11)   NOT NULL                               ,
        SendUID                    int(11)   NOT NULL                               ,
        ReceiveUID                 int(11)   NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        SendTime                   timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        TheOther                   int(11)            DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE classfilelist                (
        ClassFileID                int(11)   NOT NULL                               ,
        ClassID                    int(11)   NOT NULL                               ,
        UID                        int(11)   NOT NULL                               ,
        FileID                     int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE Homeworkuploadchecklist (
        HomeworkUploadCheckID int(11)   NOT NULL                               ,
        HomeworkUploadID      int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text               DEFAULT NULL                  ,
        FileName                   text               DEFAULT NULL                  ,
        CheckTime                  timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE Homeworkuploadfilelist  (
        HomeworkUploadFileID  int(11)   NOT NULL                               ,
        HomeworkID            int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        FileName                   text      NOT NULL                               ,
        FileType                   text      NOT NULL                               ,
        FileSize                   text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE Homeworkuploadlist      (
        HomeworkUploadID      int(11)   NOT NULL                               ,
        HomeworkID                 int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text               DEFAULT NULL                  ,
        FileList                   text               DEFAULT NULL                  ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        Status                     int(11)   NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE Homeworklist            (
        HomeworkID            int(11)   NOT NULL                               ,
        ClassID                    int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Title                      text      NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        CreateTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        EndTime                    datetime  NOT NULL                               ,
        NeedUpload                 int(11)   NOT NULL                               ,
        CanUploadAfterEnd          int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE classlist                    (
        ClassID                    int(11)   NOT NULL                               ,
        ClassName                  text      NOT NULL                               ,
        ClassAdmin                 int(11)   NOT NULL                               ,
        ClassTeacher               text      NOT NULL                               ,
        ClassMember                text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE clockinlist                  (
        ClockInID                  int(11)   NOT NULL                               ,
        ClassID                    int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Title                      text      NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        CreateTime                 date      NOT NULL DEFAULT '2000-01-01',
        EndTime                    date      NOT NULL DEFAULT '2000-01-01',
        CanUploadAfterEnd          int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE clockinuploadchecklist       (
        ClockInUploadCheckID       int(11)   NOT NULL                               ,
        ClockInUploadID            int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        CheckTime                  timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE clockinuploadfilelist        (
        ClockInUploadFileID        int(11)   NOT NULL                               ,
        ClockInID                  int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
        FileName                   text      NOT NULL                               ,
        FileType                   text      NOT NULL                               ,
        FileSize                   text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE clockinuploadlist            (
        ClockInUploadID            int(11)   NOT NULL                               ,
        ClockInID                  int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text               DEFAULT NULL                  ,
        FileList                   text               DEFAULT NULL                  ,
        UploadDate                 date      NOT NULL                               ,
        UploadTime                 time      NOT NULL DEFAULT '00:00:00'            ,
        Status                     int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE filelist                     (
        ID                         int(11)   NOT NULL                               ,
        uploaduid                  int(11)   NOT NULL                               ,
        filename                   text      NOT NULL                               ,
        filetype                   text      NOT NULL                               ,
        filesize                   int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE notice                       (
        NoticeID                   int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        Title                      text      NOT NULL                               ,
        Data                       text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE pagecount                    (
        PageCountID                int(11)   NOT NULL                               ,
        URI                        int(11)   NOT NULL                               ,
        UID                        int(11)   NOT NULL                               ,
        Time                       timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE temppassword                 (
        UserName                   text      NOT NULL                               ,
        Password                   text      NOT NULL                               ,
        Number                     text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); $Connect->query("CREATE TABLE userlist                     (
        UID                        int(11)   NOT NULL                               ,
        UserName                   text      NOT NULL                               ,
        Password                   text      NOT NULL                               ,
        UserType                   int(1)    NOT NULL                               ,
        LastLoginTime              timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; "); 
    
    $Connect->query("ALTER TABLE chatlist                ADD PRIMARY KEY (ID                   ), ADD UNIQUE KEY ID                    (ID                   ); ");
    $Connect->query("ALTER TABLE classfilelist           ADD PRIMARY KEY (ClassFileID          ), ADD UNIQUE KEY ClassFileID           (ClassFileID          ); ");
    $Connect->query("ALTER TABLE Homeworkuploadchecklist ADD PRIMARY KEY (HomeworkUploadCheckID), ADD UNIQUE KEY HomeworkUploadCheckID (HomeworkUploadCheckID); ");
    $Connect->query("ALTER TABLE Homeworkuploadfilelist  ADD PRIMARY KEY (HomeworkUploadFileID ), ADD UNIQUE KEY HomeworkUploadFileID  (HomeworkUploadFileID ); ");
    $Connect->query("ALTER TABLE Homeworkuploadlist      ADD PRIMARY KEY (HomeworkUploadID     ), ADD UNIQUE KEY HomeworkUploadID      (HomeworkUploadID     ); ");
    $Connect->query("ALTER TABLE Homeworklist            ADD PRIMARY KEY (HomeworkID           ), ADD UNIQUE KEY HomeworkID            (HomeworkID           ); ");
    $Connect->query("ALTER TABLE classlist               ADD PRIMARY KEY (ClassID              ), ADD UNIQUE KEY ClassID               (ClassID              ); ");
    $Connect->query("ALTER TABLE clockinlist             ADD PRIMARY KEY (ClockInID            ), ADD UNIQUE KEY ClockInID             (ClockInID            ); ");
    $Connect->query("ALTER TABLE clockinuploadchecklist  ADD PRIMARY KEY (ClockInUploadCheckID ), ADD UNIQUE KEY ClockInUploadCheckID  (ClockInUploadCheckID ); ");
    $Connect->query("ALTER TABLE clockinuploadfilelist   ADD PRIMARY KEY (ClockInUploadFileID  ), ADD UNIQUE KEY ClockInUploadFileID   (ClockInUploadFileID  ); ");
    $Connect->query("ALTER TABLE clockinuploadlist       ADD PRIMARY KEY (ClockInUploadID      ), ADD UNIQUE KEY ClockInUploadID       (ClockInUploadID      ); ");
    $Connect->query("ALTER TABLE filelist                ADD PRIMARY KEY (ID                   ), ADD UNIQUE KEY ID                    (ID                   ); ");
    $Connect->query("ALTER TABLE pagecount               ADD PRIMARY KEY (PageCountID          ), ADD UNIQUE KEY PageCountID           (PageCountID          ); ");
    $Connect->query("ALTER TABLE notice                  ADD PRIMARY KEY (NoticeID             ), ADD UNIQUE KEY NoticeID              (NoticeID             ); ");
    $Connect->query("ALTER TABLE userlist                ADD PRIMARY KEY (UID                  ), ADD UNIQUE KEY UID                   (UID                  ); ");
    
    $Connect->query("ALTER TABLE chatlist                MODIFY ID                    int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE classfilelist           MODIFY ClassFileID           int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE Homeworkuploadchecklist MODIFY HomeworkUploadCheckID int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE Homeworkuploadfilelist  MODIFY HomeworkUploadFileID  int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE Homeworkuploadlist      MODIFY HomeworkUploadID      int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE Homeworklist            MODIFY HomeworkID            int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE classlist               MODIFY ClassID               int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE clockinlist             MODIFY ClockInID             int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE clockinuploadchecklist  MODIFY ClockInUploadCheckID  int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE clockinuploadfilelist   MODIFY ClockInUploadFileID   int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE clockinuploadlist       MODIFY ClockInUploadID       int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE filelist                MODIFY ID                    int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE pagecount               MODIFY PageCountID           int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE notice                  MODIFY NoticeID              int(11) NOT NULL AUTO_INCREMENT; ");
    $Connect->query("ALTER TABLE userlist                MODIFY UID                   int(11) NOT NULL AUTO_INCREMENT; ");
    
    $UserName = "Admin";       $UserType = 2; $Password = EncodePassword("rna0BQoTn3(xT1M*"); $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?, ?, ?)"); $Temp->bind_param("ssi", $UserName, $Password, $UserType); $Temp->execute();
    $UserName = "TestTeacher"; $UserType = 1; $Password = EncodePassword("3*3RCzWhdME$^d4q"); $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?, ?, ?)"); $Temp->bind_param("ssi", $UserName, $Password, $UserType); $Temp->execute();
    $UserName = "TestStudent"; $UserType = 0; $Password = EncodePassword("SBSzEm6TQuTzHwHf"); $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?, ?, ?)"); $Temp->bind_param("ssi", $UserName, $Password, $UserType); $Temp->execute();
    $UserName = "Zhangting";   $UserType = 1; $Password = EncodePassword("bK5IX47wqvWAAkre"); $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?, ?, ?)"); $Temp->bind_param("ssi", $UserName, $Password, $UserType); $Temp->execute();
    $UserName = "Liujunhua";   $UserType = 1; $Password = EncodePassword("PgQqYMOQ]tQ_840("); $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?, ?, ?)"); $Temp->bind_param("ssi", $UserName, $Password, $UserType); $Temp->execute();
    $UserName = "Yuqinwen";    $UserType = 1; $Password = EncodePassword("ZdpInd_0k)T0F1wV"); $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?, ?, ?)"); $Temp->bind_param("ssi", $UserName, $Password, $UserType); $Temp->execute();

    $Numbers = array(
               /* 0 */                /* 1 */                /* 2 */                /* 3 */                /* 4 */                /* 5 */                /* 6 */                /* 7 */                /* 8 */                /* 9 */         
/* 0 */                        "3101151001220201059", "3101151001220201047", "3101151001220201078", "3101151001220201060", "3101151001220201041", "3101151001220201081", "3101151001220201065", "3101151001220201034", "3101151001220201073", 
/* 1 */ "3101151001220201079", "3101151001220201040", "3101151001220201071", "3101151001220201036", "3101151001220201052", "3101151001220201056", "3101151001220201055", "3101151001220201080", "3101151001220201070", "3101151001220201053", 
/* 2 */ "3101151001220201035", "3101151001220201043", "3101151001220201069", "3101151001220201061", "3101151001220201042", "3101151001220201038", "3101151001220201048", "3101151001220201039", "3101151001220201045", "3101151001220201067", 
/* 3 */ "3101151001220201068", "3101151001220201063", "3101151001220201066", "3101151001220201054", "3101151001220201057", "3101151001220201332", "3101151001220201050", "3101151001220201049", "3101151001220201074", "3101151001220201044", 
/* 4 */ "3101151001220201076", "3101151001220201058", "3101151001220201082", "3101151001220201064", "3101151001220201062", "3101151001220201037", "3101151001220201051", "3101151001220201072", "3101151001220201075", "3101151001220201046", 
/* 5 */ "3101151001220201077"
    );

    for ($Index = 1; $Index <= 50; $Index++) {
        $UserName = "23";
        $UserType = 0;
        $Password = "";
        if ($Index < 10) $UserName .= "0";
        $UserName .= $Index;
        $Password = CreateRandPassword();
        $Temp = $Connect->prepare("INSERT INTO TempPassword(UserName, Password, Number) VALUES (?, ?, ?)");
        $Temp->bind_param("sss", $UserName, $Password, $Numbers[$Index - 1]);
        $Temp->execute();
        $Password = EncodePassword($Password);
        $Temp = $Connect->prepare("INSERT INTO userlist(UserName, Password, UserType) VALUES (?, ?, ?)");
        $Temp->bind_param("ssi", $UserName, $Password, $UserType);
        $Temp->execute();
    }
    $ClassName = "建平西校初一23班";
    $ClassAdmin = "4";
    $ClassTeacher = "5,6,";
    $ClassMember = "7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,";
    $Temp = $Connect->prepare("INSERT INTO classlist(ClassName, ClassAdmin, ClassTeacher, ClassMember) VALUES (?,?,?,?)");
    $Temp->bind_param("ssss", $ClassName, $ClassAdmin, $ClassTeacher, $ClassMember);
    $Temp->execute();

    $ZipFile = new \ZipArchive;
    $ZipFile->open('Data.zip');
    $ZipFile->extractTo('./');
    $ZipFile->close();
    DeleteDir("ClockInDownloadFile");     mkdir("ClockInDownloadFile");
    DeleteDir("ClockInUploadFile");       mkdir("ClockInUploadFile");
    DeleteDir("HomeworkDownloadFile");    mkdir("HomeworkDownloadFile");
    DeleteDir("HomeworkUploadCheckFile"); mkdir("HomeworkUploadCheckFile");
    DeleteDir("HomeworkUploadFile");      mkdir("HomeworkUploadFile");
    DeleteDir("UploadFile");              mkdir("UploadFile");
    echo "OK";
?>
