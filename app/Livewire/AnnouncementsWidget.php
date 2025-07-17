<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Announcement;

class AnnouncementsWidget extends Component
{
    public $announcements;
    public $showModal = false;
    public $modalTitle = '';
    public $modalContent = '';

    // This method runs when the component is first loaded
    public function mount()
    {
        $this->announcements = Announcement::latest()->limit(5)->get();
    }

    // This method is called when an announcement is clicked
    public function openAnnouncement($announcementId)
    {
        $announcement = Announcement::find($announcementId);
        if ($announcement) {
            $this->modalTitle = $announcement->title;
            $this->modalContent = $announcement->content;
            $this->showModal = true;
        }
    }

    public function render()
    {
        return view('livewire.announcements-widget');
    }
}