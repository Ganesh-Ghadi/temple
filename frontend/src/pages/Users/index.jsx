import React, { useMemo, useState } from "react";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableFooter,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import axios from "axios";

import {
  File,
  PlusCircle,
  Search,
  Pencil,
  Trash,
  MoreHorizontal,
  ListFilter,
} from "lucide-react";

import Pagination from "@/customComponents/Pagination/Pagination";
import { ScrollArea, ScrollBar } from "@/components/ui/scroll-area";

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { useQuery } from "@tanstack/react-query";
import { useNavigate } from "react-router-dom";
import Delete from "./Delete";
import { Input } from "@/components/ui/input";

const Index = () => {
  const [search, setSearch] = useState("");
  const user = JSON.parse(localStorage.getItem("user"));
  const [currentPage, setCurrentPage] = useState(1);

  const token = user.token;
  const navigate = useNavigate();

  const {
    data: UsersData,
    isLoading: isUsersDataLoading,
    isError: isUsersDataError,
  } = useQuery({
    queryKey: ["users", currentPage, search], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get("/api/profiles", {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          params: {
            page: currentPage,
            search: search, // Send the current page number in the request
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  // pagination start
  const { Profiles, pagination } = UsersData || {}; // Destructure Profiles and pagination from UsersData
  const { current_page, last_page, total, per_page } = pagination || {}; // Destructure pagination data

  // Directly use Profiles for the table data
  const currentTableData = Profiles || []; // Just assign Profiles directly

  // pagination end

  if (isUsersDataError) {
    return <p>Error fetching data</p>;
  }

  return (
    <>
      <div className="w-full p-5">
        <div className="w-full mb-7 pt-4">
          <Button
            onClick={() => navigate("/users/create")}
            variant=""
            className="text-sm dark:text-white shadow-xl bg-blue-600 hover:bg-blue-700"
          >
            Add Users
          </Button>
        </div>
        <div className="px-5 dark:bg-gray-800 pt-1 w-full bg-white shadow-xl border rounded-md">
          <div className="w-full py-3 flex flex-col gap-2 md:flex-row justify-between items-center">
            <h2 className="text-xl font-medium">Users</h2>
            {/* search field here */}
            <Input
              type="text"
              value={search}
              onChange={(e) => {
                setSearch(e.target.value);
              }}
              className=" md:w-[35%] text-sm"
              placeholder="search users..."
            />
          </div>
          <Table className="mb-2">
            <TableCaption className="mb-2">
              <div className="flex justify-end">
                <Pagination
                  className="pagination-bar"
                  currentPage={current_page}
                  totalCount={total}
                  pageSize={per_page}
                  onPageChange={(page) => setCurrentPage(page)}
                  lastPage={last_page} // Pass the last_page value here
                />
              </div>
            </TableCaption>
            <TableHeader className="dark:bg-gray-900 bg-gray-100  rounded-md">
              <TableRow>
                <TableHead className="">Users</TableHead>{" "}
                {/*removed w-[100px] from here */}
                <TableHead className="text-right">Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {currentTableData &&
                currentTableData.map((user) => (
                  <TableRow
                    key={user.id}
                    className=" dark:border-b dark:border-gray-600"
                  >
                    <TableCell className="font-medium">
                      {user.profile_name}
                    </TableCell>

                    <TableCell className="text-right">
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button variant="ghost" className="h-8 w-8 p-0">
                            <span className="sr-only">Open menu</span>
                            <MoreHorizontal className="h-4 w-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                          align="center"
                          className="w-full flex-col items-center flex justify-center"
                        >
                          <DropdownMenuLabel>Actions</DropdownMenuLabel>
                          <b className="border border-gray-100 w-full"></b>
                          <Button
                            variant="ghost"
                            size="sm"
                            className="w-full text-sm"
                            onClick={() => navigate(`/users/${user.id}/edit`)}
                          >
                            <Pencil /> Edit
                          </Button>
                          <div className="w-full">
                            <Delete id={user.id} />
                          </div>
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </TableCell>
                  </TableRow>
                ))}
            </TableBody>
          </Table>
        </div>
      </div>
    </>
  );
};

export default Index;
